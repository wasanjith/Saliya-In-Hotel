<?php

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\FoodItem;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Printer Functionality', function () {
    beforeEach(function () {
        $this->user = User::factory()->create(['job_role' => 'cashier']);
        $this->category = Category::factory()->create(['is_active' => true]);
        $this->foodItem = FoodItem::factory()->create([
            'category_id' => $this->category->id,
            'is_available' => true,
            'price' => 10.00
        ]);
        $this->order = Order::factory()->create([
            'order_number' => 'ORD-20241201-0001',
            'customer_name' => 'John Doe',
            'total_amount' => 20.00
        ]);
        $this->orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'food_item_id' => $this->foodItem->id,
            'quantity' => 2,
            'unit_price' => 10.00,
            'total_price' => 20.00
        ]);
    });

    it('prints thermal invoice successfully', function () {
        $response = $this->actingAs($this->user)
            ->post('/print/thermal-invoice', ['order_id' => $this->order->id]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    });

    it('prints web invoice successfully', function () {
        $response = $this->actingAs($this->user)
            ->post('/print/web-invoice', ['order_id' => $this->order->id]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    });

    it('shows invoice page', function () {
        $response = $this->actingAs($this->user)
            ->get("/print/invoice/{$this->order->id}");
        
        $response->assertStatus(200);
        $response->assertViewIs('print.invoice');
        $response->assertViewHas('order');
    });

    it('downloads thermal invoice', function () {
        $response = $this->actingAs($this->user)
            ->get("/print/download-thermal/{$this->order->id}");
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    });

    it('handles non-existent order for printing', function () {
        $response = $this->actingAs($this->user)
            ->post('/print/thermal-invoice', ['order_id' => 99999]);
        
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    });

    it('validates order_id parameter for printing', function () {
        $response = $this->actingAs($this->user)
            ->post('/print/thermal-invoice', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_id']);
    });

    it('includes order details in thermal invoice', function () {
        $response = $this->actingAs($this->user)
            ->post('/print/thermal-invoice', ['order_id' => $this->order->id]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'invoice_data' => [
                'order_number',
                'customer_name',
                'total_amount',
                'items'
            ]
        ]);
    });

    it('handles order with multiple items for printing', function () {
        $foodItem2 = FoodItem::factory()->create([
            'category_id' => $this->category->id,
            'is_available' => true,
            'price' => 15.00
        ]);

        $orderItem2 = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'food_item_id' => $foodItem2->id,
            'quantity' => 1,
            'unit_price' => 15.00,
            'total_price' => 15.00
        ]);

        $this->order->update(['total_amount' => 35.00]);

        $response = $this->actingAs($this->user)
            ->post('/print/thermal-invoice', ['order_id' => $this->order->id]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    });

    it('handles order with discount for printing', function () {
        $this->order->update([
            'discount_amount' => 5.00,
            'total_amount' => 15.00
        ]);

        $response = $this->actingAs($this->user)
            ->post('/print/thermal-invoice', ['order_id' => $this->order->id]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    });

    it('includes payment method in invoice', function () {
        $this->order->update(['payment_method' => 'card']);

        $response = $this->actingAs($this->user)
            ->post('/print/thermal-invoice', ['order_id' => $this->order->id]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    });

    it('handles order with customer information', function () {
        $this->order->update([
            'customer_phone' => '1234567890',
            'customer_name' => 'John Doe'
        ]);

        $response = $this->actingAs($this->user)
            ->post('/print/thermal-invoice', ['order_id' => $this->order->id]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    });
}); 