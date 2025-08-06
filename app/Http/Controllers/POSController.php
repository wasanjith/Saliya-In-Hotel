<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Customer;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;

use Illuminate\Support\Facades\Log;


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

        return view('pos.index', compact('categories', 'foodItems', 'featuredItems'));
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
        Log::info('Store Order Request:', [
            'request_data' => $request->all(),
            'items_count' => count($request->items ?? [])
        ]);

        $request->validate([
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.portion' => 'nullable|in:full,half',
            'payment_method' => 'required|in:cash,card,gift,other',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'discount_amount' => 'nullable|numeric|min:0',
            'customer_paid' => 'nullable|numeric|min:0',
            'balance_returned' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Handle customer creation/lookup
            $customer = null;
            if ($request->customer_phone) {
                $customer = Customer::findOrCreateByPhone($request->customer_phone, $request->customer_name);
            }

            // Create a new order
            $order = Order::create([
                'order_type' => $request->order_type,
                'payment_method' => $request->payment_method,
                'customer_id' => $customer?->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => 0,
                'status' => 'pending',
            ]);
            
            Log::info('Created new order:', [
                'order_id' => $order->id,
                'order_type' => $order->order_type,
                'payment_method' => $order->payment_method
            ]);



            // Prepare items array for JSON storage
            $itemsArray = [];
            $subtotal = 0;

            foreach ($request->items as $item) {
                $foodItem = FoodItem::findOrFail($item['food_item_id']);
                
                if (!$foodItem->is_available) {
                    throw new \Exception("Food item '{$foodItem->name}' is not available");
                }
                
                // Get portion and order type
                $portion = $item['portion'] ?? 'full';
                $orderType = $request->order_type === 'takeaway' ? 'takeaway' : 'dine_in';
                
                // Get price based on portion and order type
                $price = $foodItem->getPrice($portion, $orderType);
                $totalPrice = $price * $item['quantity'];

                // Create item name with portion
                $itemName = $foodItem->name;
                if ($portion === 'half' && $foodItem->has_half_portion) {
                    $itemName .= ' (' . $foodItem->getPortionName('half') . ')';
                } else {
                    $itemName .= ' (' . $foodItem->getPortionName('full') . ')';
                }

                // Add item to the JSON array
                $itemsArray[] = [
                    'food_item_id' => $item['food_item_id'],
                    'item_name' => $itemName,
                    'quantity' => $item['quantity'],
                    'unit_price' => floatval($price),
                    'total_price' => floatval($totalPrice),
                    'portion' => $portion,
                    'notes' => $item['notes'] ?? null,
                ];

                $subtotal += $totalPrice;
            }

            // Create a single OrderItem record with all items in JSON format
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'items' => $itemsArray,
                'total_amount' => $subtotal,
            ]);

            // Debug logging
            Log::info('Order Items Created:', [
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'items_count' => count($itemsArray),
                'items_data' => $itemsArray,
                'subtotal' => $subtotal
            ]);

            // Calculate tax and total (you can adjust tax rate as needed)
            $taxRate = 0.10; // 10% tax for takeaway
            $taxAmount = round($subtotal * $taxRate, 2);
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount = $request->total_amount ?? ($subtotal + $taxAmount - $discountAmount);

            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'customer_paid' => $request->customer_paid ?? $totalAmount,
                'balance_returned' => $request->balance_returned ?? 0,
                'status' => $request->customer_paid ? 'completed' : 'pending',
                'completed_at' => $request->customer_paid ? now() : null,
            ]);

            // Increment customer orders_qty if order is completed and customer exists
            if ($request->customer_paid && $customer) {
                $customer->incrementOrdersQty();
            }

            DB::commit();

            // Final verification logging
            $order->refresh();
            Log::info('Final order state:', [
                'order_id' => $order->id,
                'order_type' => $order->order_type,
                'payment_method' => $order->payment_method,
                'order_items_count' => $order->orderItems()->count(),
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
                'order_items_details' => $order->orderItems()->get()->toArray()
            ]);

            return response()->json([
                'success' => true,
                'order' => $order->load('orderItems'),
                'message' => 'Order placed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOrderDetails(Order $order)
    {
        try {
            // Load the order with its items and customer
            $order->load(['orderItems', 'customer']);

            // Debug logging for order retrieval
            Log::info('Order Details Request:', [
                'order_id' => $order->id,
                'order_items_count' => $order->orderItems->count(),
                'subtotal' => $order->subtotal,
                'total_amount' => $order->total_amount,
            ]);

            // Transform the order data to include individual items from JSON structure
            $orderData = $order->toArray();
            
            // Extract individual items from JSON structure for easier frontend consumption
            $allItems = [];
            foreach ($order->orderItems as $orderItem) {
                // Check if items field exists and is not null
                if ($orderItem->items && is_array($orderItem->items)) {
                    foreach ($orderItem->items as $item) {
                        if (is_array($item) && isset($item['food_item_id'])) {
                            $allItems[] = [
                                'id' => $item['food_item_id'],
                                'food_item_id' => $item['food_item_id'],
                                'item_name' => $item['item_name'] ?? 'Unknown Item',
                                'quantity' => $item['quantity'] ?? 1,
                                'unit_price' => $item['unit_price'] ?? 0,
                                'total_price' => $item['total_price'] ?? 0,
                                'notes' => $item['notes'] ?? null,
                                'portion' => $item['portion'] ?? 'full',
                            ];
                        }
                    }
                }
            }
            
            // Add the extracted items to the order data for backward compatibility
            $orderData['order_items'] = $allItems;

            // Return the order details as JSON
            return response()->json([
                'success' => true,
                'order' => $orderData,
                'order_items' => $allItems
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting order details:', [
                'order_id' => $order->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading order details: ' . $e->getMessage()
            ], 500);
        }
    }
}
