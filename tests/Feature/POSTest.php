<?php

use App\Models\User;
use App\Models\Category;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('POS System', function () {
    beforeEach(function () {
        $this->user = User::factory()->create(['job_role' => 'cashier']);
        $this->category = Category::factory()->create(['is_active' => true]);
        $this->foodItem = FoodItem::factory()->create([
            'category_id' => $this->category->id,
            'is_available' => true,
            'price' => 10.00
        ]);
    });

    it('displays POS interface with categories and food items', function () {
        $response = $this->actingAs($this->user)->get('/pos');
        
        $response->assertStatus(200);
        $response->assertViewIs('pos.index');
        $response->assertViewHas('categories');
        $response->assertViewHas('foodItems');
        $response->assertViewHas('featuredItems');
    });

    it('returns food items by category', function () {
        $response = $this->actingAs($this->user)
            ->get("/pos/category/{$this->category->id}/items");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'price',
                'category_id',
                'is_available'
            ]
        ]);
    });

    it('creates a new order successfully', function () {
        $orderData = [
            'order_type' => 'dine_in',
            'items' => [
                [
                    'food_item_id' => $this->foodItem->id,
                    'quantity' => 2,
                    'portion' => 'full'
                ]
            ],
            'payment_method' => 'cash',
            'customer_name' => 'John Doe',
            'customer_phone' => '1234567890',
            'total_amount' => 20.00
        ];

        $response = $this->actingAs($this->user)
            ->post('/pos/order', $orderData);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'order_id',
            'order_number'
        ]);

        $this->assertDatabaseHas('orders', [
            'order_type' => 'dine_in',
            'customer_name' => 'John Doe',
            'customer_phone' => '1234567890',
            'payment_method' => 'cash'
        ]);

        $this->assertDatabaseHas('order_items', [
            'food_item_id' => $this->foodItem->id,
            'quantity' => 2
        ]);
    });

    it('validates required fields when creating order', function () {
        $response = $this->actingAs($this->user)
            ->post('/pos/order', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_type', 'items', 'payment_method']);
    });

    it('handles customer creation when phone number is provided', function () {
        $orderData = [
            'order_type' => 'takeaway',
            'items' => [
                [
                    'food_item_id' => $this->foodItem->id,
                    'quantity' => 1,
                    'portion' => 'full'
                ]
            ],
            'payment_method' => 'card',
            'customer_name' => 'Jane Smith',
            'customer_phone' => '9876543210'
        ];

        $response = $this->actingAs($this->user)
            ->post('/pos/order', $orderData);
        
        $response->assertStatus(200);

        $this->assertDatabaseHas('customers', [
            'name' => 'Jane Smith',
            'phone' => '9876543210'
        ]);
    });

    it('calculates order totals correctly', function () {
        $foodItem2 = FoodItem::factory()->create([
            'category_id' => $this->category->id,
            'is_available' => true,
            'price' => 15.00
        ]);

        $orderData = [
            'order_type' => 'dine_in',
            'items' => [
                [
                    'food_item_id' => $this->foodItem->id,
                    'quantity' => 2,
                    'portion' => 'full'
                ],
                [
                    'food_item_id' => $foodItem2->id,
                    'quantity' => 1,
                    'portion' => 'full'
                ]
            ],
            'payment_method' => 'cash',
            'total_amount' => 35.00
        ];

        $response = $this->actingAs($this->user)
            ->post('/pos/order', $orderData);
        
        $response->assertStatus(200);

        $order = Order::latest()->first();
        $this->assertEquals(35.00, $order->total_amount);
    });

    it('handles discount amounts correctly', function () {
        $orderData = [
            'order_type' => 'dine_in',
            'items' => [
                [
                    'food_item_id' => $this->foodItem->id,
                    'quantity' => 1,
                    'portion' => 'full'
                ]
            ],
            'payment_method' => 'cash',
            'discount_amount' => 2.00,
            'total_amount' => 8.00
        ];

        $response = $this->actingAs($this->user)
            ->post('/pos/order', $orderData);
        
        $response->assertStatus(200);

        $order = Order::latest()->first();
        $this->assertEquals(2.00, $order->discount_amount);
        $this->assertEquals(8.00, $order->total_amount);
    });

    it('returns order details correctly', function () {
        $order = Order::factory()->create([
            'order_type' => 'dine_in',
            'payment_method' => 'cash'
        ]);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'food_item_id' => $this->foodItem->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($this->user)
            ->get("/orders/{$order->id}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'order' => [
                'id',
                'order_number',
                'order_type',
                'payment_method',
                'total_amount'
            ],
            'items' => [
                '*' => [
                    'id',
                    'food_item_id',
                    'quantity',
                    'unit_price',
                    'total_price'
                ]
            ]
        ]);
    });
}); 