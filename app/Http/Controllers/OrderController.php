<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index()
    {
        $orders = Order::with(['customer', 'orderItems'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        // This method will be implemented if needed for manual order creation
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        try {
            // Load the order with its items and customer
            $order->load(['orderItems', 'customer']);

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
                                'rice_type' => $item['rice_type'] ?? null,
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
            return response()->json([
                'success' => false,
                'message' => 'Error loading order details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        // This method will be implemented if needed for order editing
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order)
    {
        try {
            $order->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order deleted successfully!'
                ]);
            }
            
            return redirect()->route('orders.index')
                ->with('success', 'Order deleted successfully!');
                
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting order: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('orders.index')
                ->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }

    /**
     * Search orders by order number, customer name, or phone
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $orders = Order::with(['customer', 'orderItems'])
            ->where('order_number', 'like', "%{$query}%")
            ->orWhereHas('customer', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->orWhere('customer_name', 'like', "%{$query}%")
            ->orWhere('customer_phone', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($orders);
    }

    /**
     * Get order statistics
     */
    public function statistics()
    {
        $stats = [
            'total_orders' => Order::count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'confirmed_orders' => Order::where('status', 'confirmed')->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'recent_orders' => Order::with('customer')->orderBy('created_at', 'desc')->limit(10)->get(),
        ];

        return view('orders.statistics', compact('stats'));
    }
}
