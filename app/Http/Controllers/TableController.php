<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class TableController extends Controller
{
    public function index()
    {
        return view('tables.index');
    }
    
    public function getTables()
    {
        try {
            // For now, return mock data. In a real app, you'd have a tables database table
            $tables = $this->generateDefaultTables();
            
            return response()->json([
                'success' => true,
                'tables' => $tables
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting tables: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading tables'
            ], 500);
        }
    }
    
    public function assignTable(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'table_number' => 'required|integer|min:1|max:50'
        ]);
        
        try {
            $order = Order::findOrFail($request->order_id);
            
            // Update order with table number
            $order->update([
                'table_number' => $request->table_number,
                'status' => 'confirmed'
            ]);
            
            Log::info('Table assigned successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'table_number' => $request->table_number
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Table assigned successfully',
                'order' => $order->fresh()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error assigning table: ' . $e->getMessage(), [
                'order_id' => $request->order_id,
                'table_number' => $request->table_number
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error assigning table: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function clearTable(Request $request)
    {
        $request->validate([
            'table_number' => 'required|integer|min:1|max:50'
        ]);
        
        try {
            // Find and update orders for this table
            $orders = Order::where('table_number', $request->table_number)
                ->where('status', 'confirmed')
                ->get();
            
            foreach ($orders as $order) {
                $order->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
            }
            
            Log::info('Table cleared successfully', [
                'table_number' => $request->table_number,
                'orders_completed' => $orders->count()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Table cleared successfully',
                'orders_completed' => $orders->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing table: ' . $e->getMessage(), [
                'table_number' => $request->table_number
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error clearing table: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function closeOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'table_number' => 'required|integer|min:1|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash,card,gift,other',
            'discount_amount' => 'nullable|numeric|min:0',
            'customer_paid' => 'required|numeric|min:0',
            'balance_returned' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0'
        ]);
        
        try {
            $order = Order::findOrFail($request->order_id);
            
            // Calculate tax amount (10% of subtotal)
            $taxAmount = $order->subtotal * 0.1;
            
            // Update order with payment details
            $order->update([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'payment_method' => $request->payment_method,
                'tax_amount' => $taxAmount,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => $request->total_amount,
                'customer_paid' => $request->customer_paid,
                'balance_returned' => $request->balance_returned ?? 0,
                'status' => 'completed',
                'completed_at' => now()
            ]);
            
            Log::info('Order completed successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'table_number' => $request->table_number,
                'customer_name' => $request->customer_name,
                'total_amount' => $request->total_amount
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully',
                'order' => $order->fresh()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error completing order: ' . $e->getMessage(), [
                'order_id' => $request->order_id,
                'table_number' => $request->table_number
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function generateDefaultTables()
    {
        $tables = [];
        
        // Get currently occupied tables
        $occupiedTables = Order::where('status', 'confirmed')
            ->whereNotNull('table_number')
            ->pluck('table_number', 'id')
            ->toArray();
        
        for ($i = 1; $i <= 20; $i++) {
            $isOccupied = in_array($i, $occupiedTables);
            
            $tables[] = [
                'id' => $i,
                'number' => $i,
                'capacity' => $this->getTableCapacity($i),
                'status' => $isOccupied ? 'occupied' : 'available',
                'current_order' => $isOccupied ? $this->getCurrentOrderForTable($i) : null
            ];
        }
        
        return $tables;
    }
    
    private function getTableCapacity($tableNumber)
    {
        // Small tables (1-10): 4 seats
        // Medium tables (11-15): 6 seats  
        // Large tables (16-20): 8 seats
        if ($tableNumber <= 10) {
            return 4;
        } elseif ($tableNumber <= 15) {
            return 6;
        } else {
            return 8;
        }
    }
    
    private function getCurrentOrderForTable($tableNumber)
    {
        $order = Order::where('table_number', $tableNumber)
            ->where('status', 'confirmed')
            ->first();
            
        if ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at
            ];
        }
        
        return null;
    }
}