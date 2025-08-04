<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;

class POSController extends Controller
{
    public function index()
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
        $request->validate([
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,gift,other',
        ]);

        $order = new Order();
        $order->order_type = $request->order_type;
        $order->payment_method = $request->payment_method;
        $order->subtotal = 0;
        $order->tax_amount = 0;
        $order->discount_amount = 0;
        $order->total_amount = 0;
        $order->status = 'pending';
        $order->save();

        $subtotal = 0;
        foreach ($request->items as $item) {
            $foodItem = FoodItem::find($item['food_item_id']);
            $price = $request->order_type === 'takeaway' ? $foodItem->takeaway_price : $foodItem->dine_in_price;
            $totalPrice = $price * $item['quantity'];

            OrderItem::create([
                'order_id' => $order->id,
                'food_item_id' => $item['food_item_id'],
                'item_name' => $foodItem->name,
                'quantity' => $item['quantity'],
                'unit_price' => $price,
                'total_price' => $totalPrice,
            ]);

            $subtotal += $totalPrice;
        }

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

        return response()->json([
            'success' => true,
            'order' => $order->load('orderItems'),
            'message' => 'Order placed successfully!'
        ]);
    }
}
