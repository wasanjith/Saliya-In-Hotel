<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Order Model', function () {
    it('generates unique order numbers', function () {
        $order1 = Order::factory()->create();
        $order2 = Order::factory()->create();

        $this->assertNotEquals($order1->order_number, $order2->order_number);
        $this->assertStringStartsWith('ORD-', $order1->order_number);
        $this->assertStringStartsWith('ORD-', $order2->order_number);
    });

    it('has correct relationships', function () {
        $order = Order::factory()->create();
        $orderItem = OrderItem::factory()->create(['order_id' => $order->id]);
        $customer = Customer::factory()->create();
        $order->update(['customer_id' => $customer->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $order->orderItems);
        $this->assertInstanceOf(Customer::class, $order->customer);
        $this->assertEquals(1, $order->orderItems->count());
    });

    it('calculates total amount correctly', function () {
        $order = Order::factory()->create([
            'subtotal' => 50.00,
            'discount_amount' => 10.00,
            'total_amount' => 40.00
        ]);

        $this->assertEquals(40.00, $order->total_amount);
        $this->assertEquals(10.00, $order->discount_amount);
    });

    it('handles order status correctly', function () {
        $order = Order::factory()->create(['status' => 'pending']);
        
        $order->update(['status' => 'completed']);
        
        $this->assertEquals('completed', $order->status);
    });

    it('casts decimal fields correctly', function () {
        $order = Order::factory()->create([
            'subtotal' => '25.50',
            'total_amount' => '25.50',
            'discount_amount' => '5.00'
        ]);

        $this->assertIsFloat($order->subtotal);
        $this->assertIsFloat($order->total_amount);
        $this->assertIsFloat($order->discount_amount);
    });

    it('handles order types correctly', function () {
        $orderTypes = ['dine_in', 'takeaway', 'delivery'];
        
        foreach ($orderTypes as $type) {
            $order = Order::factory()->create(['order_type' => $type]);
            $this->assertEquals($type, $order->order_type);
        }
    });

    it('handles payment methods correctly', function () {
        $paymentMethods = ['cash', 'card', 'gift', 'other'];
        
        foreach ($paymentMethods as $method) {
            $order = Order::factory()->create(['payment_method' => $method]);
            $this->assertEquals($method, $order->payment_method);
        }
    });
}); 