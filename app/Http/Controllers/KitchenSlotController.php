<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KitchenSlot;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class KitchenSlotController extends Controller
{
    public function index()
    {
        return view('kitchen-slots.index');
    }

    public function getSlots()
    {
        try {
            $slots = KitchenSlot::where('is_active', true)
                ->orderBy('slot_number')
                ->get()
                ->map(function ($slot) {
                    return [
                        'id' => $slot->id,
                        'slot_number' => $slot->slot_number,
                        'status' => $slot->status,
                        'order_id' => $slot->order_id,
                        'occupied_at' => $slot->occupied_at,
                        'completed_at' => $slot->completed_at,
                        'notes' => $slot->notes,
                        'current_order' => $slot->order ? [
                            'id' => $slot->order->id,
                            'order_number' => $slot->order->order_number,
                            'customer_name' => $slot->order->customer_name,
                            'total_amount' => $slot->order->total_amount,
                            'created_at' => $slot->order->created_at
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'slots' => $slots
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting kitchen slots: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading kitchen slots'
            ], 500);
        }
    }

    public function assignSlot(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'slot_number' => 'required|string'
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            $slot = KitchenSlot::where('slot_number', $request->slot_number)->first();

            if (!$slot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kitchen slot not found'
                ], 404);
            }

            if (!$slot->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kitchen slot is not active'
                ], 400);
            }

            if ($slot->isOccupied()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kitchen slot is currently occupied'
                ], 400);
            }

            // Update slot with order
            $slot->update([
                'order_id' => $request->order_id,
                'status' => 'occupied',
                'occupied_at' => now()
            ]);

            // Update order status
            $order->update([
                'status' => 'preparing'
            ]);

            Log::info('Kitchen slot assigned successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'slot_number' => $request->slot_number
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kitchen slot assigned successfully',
                'order' => $order->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error assigning kitchen slot: ' . $e->getMessage(), [
                'order_id' => $request->order_id,
                'slot_number' => $request->slot_number
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error assigning kitchen slot: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completeOrder(Request $request)
    {
        $request->validate([
            'slot_number' => 'required|string'
        ]);

        try {
            $slot = KitchenSlot::where('slot_number', $request->slot_number)->first();

            if (!$slot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kitchen slot not found'
                ], 404);
            }

            if (!$slot->isOccupied()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kitchen slot is not occupied'
                ], 400);
            }

            $order = $slot->order;

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'No order found for this slot'
                ], 404);
            }

            // Update slot status
            $slot->update([
                'status' => 'available',
                'order_id' => null,
                'completed_at' => now()
            ]);

            // Update order status
            $order->update([
                'status' => 'ready_for_pickup'
            ]);

            Log::info('Order completed successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'slot_number' => $request->slot_number
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully',
                'order' => $order->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error completing order: ' . $e->getMessage(), [
                'slot_number' => $request->slot_number
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function closeOrder(Request $request)
    {
        try {
            Log::info('Close Kitchen Order Request:', [
                'request_data' => $request->all()
            ]);

            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'slot_number' => 'required|string',
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'payment_method' => 'required|in:cash,card,gift',
                'discount_amount' => 'nullable|numeric|min:0',
                'customer_paid' => 'required|numeric|min:0',
                'balance_returned' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0'
            ]);

            $slot = KitchenSlot::where('slot_number', $request->slot_number)->first();

            if (!$slot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kitchen slot not found'
                ], 404);
            }

            if (!$slot->isOccupied()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kitchen slot is not occupied'
                ], 400);
            }

            $order = Order::with('orderItems')->findOrFail($request->order_id);

            if ($slot->order_id !== $order->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order is not assigned to the selected kitchen slot'
                ], 400);
            }

            // Handle customer creation/updating similar to dine-in flow
            $customer = null;
            if ($request->customer_phone) {
                $customer = Customer::where('phone', $request->customer_phone)->first();

                if ($customer) {
                    if ($customer->name !== $request->customer_name) {
                        $customer->update(['name' => $request->customer_name]);
                    }
                } else {
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
                    $customer->incrementOrdersQty();
                } else {
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

            // Free the kitchen slot
            $slot->update([
                'status' => 'available',
                'order_id' => null,
                'completed_at' => now()
            ]);

            Log::info('Kitchen order completed successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'slot_number' => $request->slot_number,
                'customer_id' => $customer->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully',
                'order' => $order->fresh(),
                'customer' => $customer
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in closeKitchenOrder:', [
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
            Log::error('Error completing kitchen order: ' . $e->getMessage(), [
                'slot_number' => $request->slot_number ?? 'unknown',
                'order_id' => $request->order_id ?? 'unknown',
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 500);
        }
    }
}
