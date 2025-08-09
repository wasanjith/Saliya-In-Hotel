<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Table;
use App\Models\Customer;
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
            // Get all active tables from the database
            $tables = Table::where('is_active', true)
                ->orderBy('number')
                ->get()
                ->map(function ($table) {
                    // Check if table is currently occupied
                    $currentOrder = $table->currentOrder();
                    
                    return [
                        'id' => $table->id,
                        'number' => $table->number,
                        'capacity' => $table->capacity,
                        'status' => $currentOrder ? 'occupied' : $table->status,
                        'description' => $table->description,
                        'location' => $table->location,
                        'current_order' => $currentOrder ? [
                            'id' => $currentOrder->id,
                            'order_number' => $currentOrder->order_number,
                            'total_amount' => $currentOrder->total_amount,
                            'created_at' => $currentOrder->created_at
                        ] : null
                    ];
                });
            
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
            $table = Table::where('number', $request->table_number)->first();
            
            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ], 404);
            }
            
            if (!$table->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table is not active'
                ], 400);
            }
            
            if ($table->isOccupied()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table is currently occupied'
                ], 400);
            }
            
            // Update order with table number
            $order->update([
                'table_number' => $request->table_number,
                'status' => 'confirmed'
            ]);
            
            // Update table status to occupied
            $table->update(['status' => 'occupied']);
            
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
            $table = Table::where('number', $request->table_number)->first();
            
            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ], 404);
            }
            
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
            
            // Update table status to available
            $table->update(['status' => 'available']);
            
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
        try {
            Log::info('Close Order Request:', [
                'request_data' => $request->all()
            ]);
            
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'table_number' => 'required|integer|min:1|max:50',
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'payment_method' => 'required|in:cash,card,gift',
                'discount_amount' => 'nullable|numeric|min:0',
                'customer_paid' => 'required|numeric|min:0',
                'balance_returned' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0'
            ]);
            
            $order = Order::with('orderItems')->findOrFail($request->order_id);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }
            
            $table = Table::where('number', $request->table_number)->first();
            
            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ], 404);
            }
            
            // Handle customer creation/updating
            $customer = null;
            if ($request->customer_phone) {
                // Try to find existing customer by phone number
                $customer = Customer::where('phone', $request->customer_phone)->first();
                
                if ($customer) {
                    // Update existing customer name if it's different
                    if ($customer->name !== $request->customer_name) {
                        $customer->update(['name' => $request->customer_name]);
                    }
                } else {
                    // Create new customer
                    $customer = Customer::create([
                        'name' => $request->customer_name,
                        'phone' => $request->customer_phone,
                        'orders_qty' => 0
                    ]);
                }
                
                // Increment customer's orders quantity
                $customer->incrementOrdersQty();
            } else {
                // If no phone number provided, try to find customer by name only
                $customer = Customer::where('name', $request->customer_name)
                    ->whereNull('phone')
                    ->first();
                
                if ($customer) {
                    // Update existing customer
                    $customer->incrementOrdersQty();
                } else {
                    // Create new customer with just name
                    $customer = Customer::create([
                        'name' => $request->customer_name,
                        'phone' => null,
                        'orders_qty' => 1
                    ]);
                }
            }
            
            // Update order with payment details and customer_id
            $order->update([
                'customer_id' => $customer->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'payment_method' => $request->payment_method,
                'total_amount' => $request->total_amount,
                'customer_paid' => $request->customer_paid,
                'balance_returned' => $request->balance_returned ?? 0,
                'status' => 'completed',
                'completed_at' => now()
            ]);
            
            // Update table status to available
            $table->update(['status' => 'available']);
            
            Log::info('Order completed successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'table_number' => $request->table_number,
                'customer_name' => $request->customer_name,
                'customer_id' => $customer->id,
                'total_amount' => $request->total_amount
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully',
                'order' => $order->fresh(),
                'customer' => $customer
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in closeOrder:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            $errorMessages = [];
            foreach ($e->errors() as $field => $errors) {
                $errorMessages = array_merge($errorMessages, $errors);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $errorMessages)
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error completing order: ' . $e->getMessage(), [
                'order_id' => $request->order_id ?? 'unknown',
                'table_number' => $request->table_number ?? 'unknown',
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 500);
        }
    }
}