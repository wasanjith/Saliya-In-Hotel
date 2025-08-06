<?php

use App\Models\User;
use App\Models\Category;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Integration Tests', function () {
    beforeEach(function () {
        $this->user = User::factory()->create(['job_role' => 'cashier']);
        $this->category = Category::factory()->create(['is_active' => true]);
        $this->foodItem = FoodItem::factory()->create([
            'category_id' => $this->category->id,
            'is_available' => true,
            'price' => 10.00
        ]);
        $this->table = Table::factory()->create([
            'table_number' => 'T1',
            'capacity' => 4,
            'status' => 'available'
        ]);
    });

    it('completes full order workflow', function () {
        // 1. Assign table to customer
        $tableResponse = $this->actingAs($this->user)
            ->post('/api/assign-table', [
                'table_number' => $this->table->table_number,
                'customer_name' => 'John Doe',
                'customer_phone' => '1234567890'
            ]);
        
        $tableResponse->assertStatus(200);

        // 2. Create order through POS
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
            'table_number' => $this->table->table_number,
            'total_amount' => 20.00
        ];

        $orderResponse = $this->actingAs($this->user)
            ->post('/pos/order', $orderData);
        
        $orderResponse->assertStatus(200);

        // 3. Verify order was created
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('dine_in', $order->order_type);
        $this->assertEquals('John Doe', $order->customer_name);

        // 4. Verify order items were created
        $this->assertEquals(1, $order->orderItems->count());
        $this->assertEquals(2, $order->orderItems->first()->quantity);

        // 5. Verify table status
        $this->table->refresh();
        $this->assertEquals('occupied', $this->table->status);

        // 6. Print invoice
        $printResponse = $this->actingAs($this->user)
            ->post('/print/thermal-invoice', ['order_id' => $order->id]);
        
        $printResponse->assertStatus(200);

        // 7. Close order
        $closeResponse = $this->actingAs($this->user)
            ->post('/api/close-order', ['order_id' => $order->id]);
        
        $closeResponse->assertStatus(200);

        // 8. Clear table
        $clearResponse = $this->actingAs($this->user)
            ->post('/api/clear-table', ['table_number' => $this->table->table_number]);
        
        $clearResponse->assertStatus(200);

        // 9. Verify final states
        $order->refresh();
        $this->table->refresh();
        
        $this->assertEquals('completed', $order->status);
        $this->assertEquals('available', $this->table->status);
    });

    it('handles customer creation and order association', function () {
        // 1. Create customer
        $customerData = [
            'name' => 'Jane Smith',
            'phone' => '9876543210',
        ];

        $customerResponse = $this->actingAs($this->user)
            ->post('/customers', $customerData);
        
        $customerResponse->assertRedirect('/customers');

        // 2. Verify customer was created
        $customer = Customer::where('phone', '9876543210')->first();
        $this->assertNotNull($customer);

        // 3. Create order with existing customer
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
            'customer_phone' => '9876543210',
            'total_amount' => 10.00
        ];

        $orderResponse = $this->actingAs($this->user)
            ->post('/pos/order', $orderData);
        
        $orderResponse->assertStatus(200);

        // 4. Verify order is associated with customer
        $order = Order::latest()->first();
        $this->assertEquals($customer->id, $order->customer_id);
    });

    it('handles order search and filtering', function () {
        // 1. Create multiple orders
        $order1 = Order::factory()->create([
            'order_number' => 'ORD-20241201-0001',
            'customer_name' => 'John Doe',
            'status' => 'completed'
        ]);

        $order2 = Order::factory()->create([
            'order_number' => 'ORD-20241201-0002',
            'customer_name' => 'Jane Smith',
            'status' => 'pending'
        ]);

        // 2. Search orders
        $searchResponse = $this->actingAs($this->user)
            ->get('/orders/search?q=John');
        
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('John Doe');
        $searchResponse->assertDontSee('Jane Smith');

        // 3. Get order statistics
        $statsResponse = $this->actingAs($this->user)
            ->get('/orders/statistics');
        
        $statsResponse->assertStatus(200);
    });

    it('handles table management workflow', function () {
        // 1. Get all tables
        $tablesResponse = $this->actingAs($this->user)->get('/api/tables');
        $tablesResponse->assertStatus(200);

        // 2. Assign table
        $assignResponse = $this->actingAs($this->user)
            ->post('/api/assign-table', [
                'table_number' => $this->table->table_number,
                'customer_name' => 'Test Customer'
            ]);
        
        $assignResponse->assertStatus(200);

        // 3. Verify table is occupied
        $this->table->refresh();
        $this->assertEquals('occupied', $this->table->status);

        // 4. Clear table
        $clearResponse = $this->actingAs($this->user)
            ->post('/api/clear-table', ['table_number' => $this->table->table_number]);
        
        $clearResponse->assertStatus(200);

        // 5. Verify table is available
        $this->table->refresh();
        $this->assertEquals('available', $this->table->status);
    });
}); 