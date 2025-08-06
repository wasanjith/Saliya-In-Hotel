<?php

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\FoodItem;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Order Management', function () {
    beforeEach(function () {
        $this->user = User::factory()->create(['job_role' => 'cashier']);
        $this->category = Category::factory()->create(['is_active' => true]);
        $this->foodItem = FoodItem::factory()->create([
            'category_id' => $this->category->id,
            'is_available' => true,
            'price' => 10.00
        ]);
    });

    it('displays orders index page', function () {
        $response = $this->actingAs($this->user)->get('/orders');
        
        $response->assertStatus(200);
        $response->assertViewIs('orders.index');
    });

    it('creates a new order successfully', function () {
        $orderData = [
            'order_type' => 'dine_in',
            'payment_method' => 'cash',
            'customer_name' => 'John Doe',
            'customer_phone' => '1234567890',
            'total_amount' => 20.00
        ];

        $response = $this->actingAs($this->user)
            ->post('/orders', $orderData);
        
        $response->assertRedirect('/orders');
        $this->assertDatabaseHas('orders', $orderData);
    });

    it('validates required fields when creating order', function () {
        $response = $this->actingAs($this->user)
            ->post('/orders', []);
        
        $response->assertSessionHasErrors(['order_type', 'payment_method']);
    });

    it('updates order successfully', function () {
        $order = Order::factory()->create();

        $updateData = [
            'order_type' => 'takeaway',
            'payment_method' => 'card',
            'status' => 'completed'
        ];

        $response = $this->actingAs($this->user)
            ->put("/orders/{$order->id}", $updateData);
        
        $response->assertRedirect('/orders');
        $this->assertDatabaseHas('orders', $updateData);
    });

    it('deletes order successfully', function () {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete("/orders/{$order->id}");
        
        $response->assertRedirect('/orders');
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    });

    it('searches orders by order number or customer', function () {
        $order = Order::factory()->create([
            'order_number' => 'ORD-20241201-0001',
            'customer_name' => 'John Doe'
        ]);

        $response = $this->actingAs($this->user)
            ->get('/orders/search?q=ORD-20241201-0001');
        
        $response->assertStatus(200);
        $response->assertViewHas('orders');
        $response->assertSee('ORD-20241201-0001');
    });

    it('returns order statistics', function () {
        Order::factory()->count(5)->create(['status' => 'completed']);
        Order::factory()->count(3)->create(['status' => 'pending']);

        $response = $this->actingAs($this->user)->get('/orders/statistics');
        
        $response->assertStatus(200);
        $response->assertViewIs('orders.statistics');
    });

    it('displays order details page', function () {
        $order = Order::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'food_item_id' => $this->foodItem->id
        ]);

        $response = $this->actingAs($this->user)
            ->get("/orders/{$order->id}");
        
        $response->assertStatus(200);
        $response->assertViewHas('order');
    });

    it('handles order status updates correctly', function () {
        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->user)
            ->patch("/orders/{$order->id}/status", ['status' => 'completed']);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed'
        ]);
    });

    it('calculates order totals correctly', function () {
        $foodItem2 = FoodItem::factory()->create([
            'category_id' => $this->category->id,
            'is_available' => true,
            'price' => 15.00
        ]);

        $order = Order::factory()->create([
            'subtotal' => 0,
            'total_amount' => 0
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'food_item_id' => $this->foodItem->id,
            'quantity' => 2,
            'unit_price' => 10.00,
            'total_price' => 20.00
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'food_item_id' => $foodItem2->id,
            'quantity' => 1,
            'unit_price' => 15.00,
            'total_price' => 15.00
        ]);

        $order->refresh();
        $this->assertEquals(35.00, $order->orderItems->sum('total_price'));
    });

    it('handles order with discount correctly', function () {
        $order = Order::factory()->create([
            'subtotal' => 50.00,
            'discount_amount' => 10.00,
            'total_amount' => 40.00
        ]);

        $this->assertEquals(40.00, $order->total_amount);
        $this->assertEquals(10.00, $order->discount_amount);
    });

    it('generates unique order numbers', function () {
        $order1 = Order::factory()->create();
        $order2 = Order::factory()->create();

        $this->assertNotEquals($order1->order_number, $order2->order_number);
        $this->assertStringStartsWith('ORD-', $order1->order_number);
        $this->assertStringStartsWith('ORD-', $order2->order_number);
    });

    it('handles order items correctly', function () {
        $order = Order::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'food_item_id' => $this->foodItem->id,
            'quantity' => 3,
            'unit_price' => 10.00,
            'total_price' => 30.00
        ]);

        $this->assertEquals(1, $order->orderItems->count());
        $this->assertEquals(30.00, $order->orderItems->first()->total_price);
    });

    it('validates order item quantities', function () {
        $orderData = [
            'order_type' => 'dine_in',
            'payment_method' => 'cash',
            'items' => [
                [
                    'food_item_id' => $this->foodItem->id,
                    'quantity' => 0 // Invalid quantity
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->post('/orders', $orderData);
        
        $response->assertSessionHasErrors(['items.0.quantity']);
    });
}); 