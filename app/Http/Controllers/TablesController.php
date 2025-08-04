<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;

class TablesController extends Controller
{
    public function index()
    {
        $tables = Table::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Load current orders for each table
        foreach ($tables as $table) {
            $table->current_order = $table->getCurrentOrder();
        }

        return view('tables.index', compact('tables'));
    }

    public function updateStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,maintenance'
        ]);

        $table->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Table status updated successfully'
        ]);
    }

    public function assignOrder(Request $request, Table $table)
    {
        // Check if table is available
        if (!$table->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Table is not available'
            ], 400);
        }

        // Check if table already has an active order
        if ($table->hasActiveOrder()) {
            return response()->json([
                'success' => false,
                'message' => 'Table already has an active order'
            ], 400);
        }

        try {
            // Create a new order for this table
            $order = Order::create([
                'order_type' => 'dine_in',
                'table_id' => $table->id,
                'status' => 'pending',
                'subtotal' => 0.00,
                'tax_amount' => 0.00,
                'discount_amount' => 0.00,
                'total_amount' => 0.00,
                'payment_method' => 'cash',
            ]);

            // Update table status to occupied
            $table->update(['status' => 'occupied']);

            return response()->json([
                'success' => true,
                'order' => $order,
                'message' => 'Order assigned to table successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTableOrders(Table $table)
    {
        $orders = $table->orders()
            ->with('orderItems')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function completeOrder(Request $request, Order $order)
    {
        try {
            // Mark the order as complete
            $order->update(['status' => 'completed']);

            // Update the associated table's status to available
            if ($order->table) {
                $order->table->update(['status' => 'available']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order completed and table is now available'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete order: ' . $e->getMessage()
            ], 500);
        }
    }
}
