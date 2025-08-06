<?php

use App\Models\User;
use App\Models\Table;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Table Management', function () {
    beforeEach(function () {
        $this->user = User::factory()->create(['job_role' => 'cashier']);
        $this->table = Table::factory()->create([
            'table_number' => 'T1',
            'capacity' => 4,
            'status' => 'available'
        ]);
    });

    it('displays tables index page', function () {
        $response = $this->actingAs($this->user)->get('/tables');
        
        $response->assertStatus(200);
        $response->assertViewIs('tables.index');
    });

    it('returns tables via API', function () {
        $response = $this->actingAs($this->user)->get('/api/tables');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'table_number',
                'capacity',
                'status',
                'current_order_id'
            ]
        ]);
    });

    it('assigns table to customer successfully', function () {
        $orderData = [
            'table_number' => $this->table->table_number,
            'customer_name' => 'John Doe',
            'customer_phone' => '1234567890'
        ];

        $response = $this->actingAs($this->user)
            ->post('/api/assign-table', $orderData);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tables', [
            'id' => $this->table->id,
            'status' => 'occupied'
        ]);

        $this->assertDatabaseHas('orders', [
            'table_number' => $this->table->table_number,
            'customer_name' => 'John Doe',
            'customer_phone' => '1234567890'
        ]);
    });

    it('validates required fields when assigning table', function () {
        $response = $this->actingAs($this->user)
            ->post('/api/assign-table', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['table_number', 'customer_name']);
    });

    it('prevents assigning already occupied table', function () {
        // First, occupy the table
        $this->table->update(['status' => 'occupied']);
        
        $orderData = [
            'table_number' => $this->table->table_number,
            'customer_name' => 'Jane Smith',
            'customer_phone' => '9876543210'
        ];

        $response = $this->actingAs($this->user)
            ->post('/api/assign-table', $orderData);
        
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    });

    it('clears table successfully', function () {
        // First, occupy the table
        $this->table->update(['status' => 'occupied']);
        $order = Order::factory()->create([
            'table_number' => $this->table->table_number,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->user)
            ->post('/api/clear-table', ['table_number' => $this->table->table_number]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tables', [
            'id' => $this->table->id,
            'status' => 'available'
        ]);
    });

    it('closes order successfully', function () {
        $order = Order::factory()->create([
            'table_number' => $this->table->table_number,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->user)
            ->post('/api/close-order', ['order_id' => $order->id]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed'
        ]);
    });

    it('handles table status updates correctly', function () {
        $table2 = Table::factory()->create([
            'table_number' => 'T2',
            'capacity' => 6,
            'status' => 'available'
        ]);

        // Test assigning table
        $response = $this->actingAs($this->user)
            ->post('/api/assign-table', [
                'table_number' => $table2->table_number,
                'customer_name' => 'Test Customer'
            ]);
        
        $response->assertStatus(200);

        $table2->refresh();
        $this->assertEquals('occupied', $table2->status);

        // Test clearing table
        $response = $this->actingAs($this->user)
            ->post('/api/clear-table', ['table_number' => $table2->table_number]);
        
        $response->assertStatus(200);

        $table2->refresh();
        $this->assertEquals('available', $table2->status);
    });

    it('returns error for non-existent table', function () {
        $response = $this->actingAs($this->user)
            ->post('/api/assign-table', [
                'table_number' => 'NON_EXISTENT',
                'customer_name' => 'Test Customer'
            ]);
        
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    });
}); 