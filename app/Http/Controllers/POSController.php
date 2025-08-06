<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;

class POSController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        $foodItems = FoodItem::where('is_available', true)
            ->with('category')
            ->orderBy('sort_order')
            ->get();
        
        $featuredItems = FoodItem::where('is_available', true)
            ->where('is_featured', true)
            ->with('category')
            ->orderBy('sort_order')
            ->get();

        // Get selected table if table_id is provided
        $selectedTable = null;
        if ($request->has('table_id')) {
            $selectedTable = Table::find($request->table_id);
        }

        return view('pos.index', compact('categories', 'foodItems', 'featuredItems', 'selectedTable'));
    }

    public function getFoodItemsByCategory($categoryId)
    {
        $foodItems = FoodItem::where('category_id', $categoryId)
            ->where('is_available', true)
            ->with('category')
            ->orderBy('sort_order')
            ->get();

        return response()->json($foodItems);
    }

    public function storeOrder(Request $request)
    {
        // Log incoming request
        \Log::info('Store Order Request:', [
            'request_data' => $request->all(),
            'order_id' => $request->order_id,
            'table_id' => $request->table_id,
            'items_count' => count($request->items ?? [])
        ]);

        $request->validate([
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,gift,other',
            'table_id' => 'nullable|exists:tables,id',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        // Check if we're updating an existing order or creating a new one
        if ($request->order_id) {
            $order = Order::findOrFail($request->order_id);
            
            \Log::info('Updating existing order:', [
                'order_id' => $order->id,
                'existing_order_items_count' => $order->orderItems()->count()
            ]);
            
            // Update the order details (in case anything changed)
            $order->update([
                'order_type' => $request->order_type,
                'table_id' => $request->table_id,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
            ]);
            
            // Clear existing order items since we're replacing them
            $deletedCount = $order->orderItems()->count();
            $order->orderItems()->delete();
            
            \Log::info('Cleared existing order items:', [
                'order_id' => $order->id,
                'deleted_items_count' => $deletedCount
            ]);
        } else {
            // Create a new order
            $order = new Order();
            $order->order_type = $request->order_type;
            $order->table_id = $request->table_id;
            $order->payment_method = $request->payment_method;
            $order->subtotal = 0;
            $order->tax_amount = 0;
            $order->discount_amount = 0;
            $order->total_amount = 0;
            $order->status = 'pending';
            $order->save();
            
            \Log::info('Created new order:', [
                'order_id' => $order->id,
                'order_type' => $order->order_type,
                'table_id' => $order->table_id
            ]);
        }

        // Update table status to occupied if it's a dine-in order with table (only for new orders)
        if ($request->order_type === 'dine_in' && $request->table_id && !$request->order_id) {
            $table = Table::find($request->table_id);
            if ($table && $table->isAvailable()) {
                $table->update(['status' => 'occupied']);
            }
        }

        // Prepare items array for JSON storage
        $itemsArray = [];
        $subtotal = 0;

        foreach ($request->items as $item) {
            $foodItem = FoodItem::find($item['food_item_id']);
            $price = $request->order_type === 'takeaway' ? $foodItem->takeaway_price : $foodItem->dine_in_price;
            $totalPrice = $price * $item['quantity'];

            // Add item to the JSON array
            $itemsArray[] = [
                'food_item_id' => $item['food_item_id'],
                'item_name' => $foodItem->name,
                'quantity' => $item['quantity'],
                'unit_price' => $price,
                'total_price' => $totalPrice,
                'notes' => $item['notes'] ?? null,
            ];

            $subtotal += $totalPrice;
        }

        // Create a single OrderItem record with all items in JSON format
        $orderItem = new OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->items = $itemsArray;
        $orderItem->total_amount = $subtotal;
        $orderItem->save();

        // Debug logging
        \Log::info('Order Items Saved:', [
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'items_count' => count($itemsArray),
            'items_data' => $itemsArray,
            'subtotal' => $subtotal
        ]);

        // Calculate tax and total (you can adjust tax rate as needed)
        $taxRate = 0.14; // 14% tax
        $taxAmount = round($subtotal * $taxRate);
        $discountRate = 0.12; // 12% discount
        $discountAmount = round($subtotal * $discountRate);
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
        ]);

        // Final verification logging
        $order->refresh();
        \Log::info('Final order state:', [
            'order_id' => $order->id,
            'order_items_count' => $order->orderItems()->count(),
            'subtotal' => $order->subtotal,
            'total_amount' => $order->total_amount,
            'order_items_details' => $order->orderItems()->get()->toArray()
        ]);

        return response()->json([
            'success' => true,
            'order' => $order->load('orderItems'),
            'message' => 'Order placed successfully!'
        ]);
    }

    public function getOrderDetails(Order $order)
    {
        // Load the order with its items
        $order->load('orderItems');

        // Debug logging for order retrieval
        \Log::info('Order Details Request:', [
            'order_id' => $order->id,
            'order_items_count' => $order->orderItems->count(),
            'raw_order_items' => $order->orderItems->toArray(),
            'subtotal' => $order->subtotal,
            'total_amount' => $order->total_amount,
        ]);

        // Transform the order data to include individual items from JSON structure
        $orderData = $order->toArray();
        
        // Extract individual items from JSON structure for easier frontend consumption
        $allItems = [];
        foreach ($order->orderItems as $orderItem) {
            if (isset($orderItem->items) && is_array($orderItem->items)) {
                foreach ($orderItem->items as $item) {
                    $allItems[] = [
                        'id' => $item['food_item_id'],
                        'food_item_id' => $item['food_item_id'],
                        'item_name' => $item['item_name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'notes' => $item['notes'] ?? null,
                    ];
                }
            }
        }
        
        // Add the extracted items to the order data for backward compatibility
        $orderData['order_items'] = $allItems;



        // Return the order details as JSON
        return response()->json($orderData);
    }
}
