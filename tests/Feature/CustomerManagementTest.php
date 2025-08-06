<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Customer Management', function () {
    beforeEach(function () {
        $this->user = User::factory()->create(['job_role' => 'cashier']);
    });

    it('displays customers index page', function () {
        $response = $this->actingAs($this->user)->get('/customers');
        
        $response->assertStatus(200);
        $response->assertViewIs('customers.index');
    });

    it('creates a new customer successfully', function () {
        $customerData = [
            'name' => 'John Doe',
            'phone' => '1234567890',
        ];

        $response = $this->actingAs($this->user)
            ->post('/customers', $customerData);
        
        $response->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', $customerData);
    });

    it('validates required fields when creating customer', function () {
        $response = $this->actingAs($this->user)
            ->post('/customers', []);
        
        $response->assertSessionHasErrors(['name', 'phone']);
    });

    it('updates customer successfully', function () {
        $customer = Customer::factory()->create();

        $updateData = [
            'name' => 'Jane Smith',
            'phone' => '9876543210',
        ];

        $response = $this->actingAs($this->user)
            ->put("/customers/{$customer->id}", $updateData);
        
        $response->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', $updateData);
    });

    it('deletes customer successfully', function () {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete("/customers/{$customer->id}");
        
        $response->assertRedirect('/customers');
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    });

    it('searches customers by name or phone', function () {
        $customer1 = Customer::factory()->create(['name' => 'John Doe', 'phone' => '1234567890']);
        $customer2 = Customer::factory()->create(['name' => 'Jane Smith', 'phone' => '9876543210']);

        $response = $this->actingAs($this->user)
            ->get('/customers/search?q=John');
        
        $response->assertStatus(200);
        $response->assertViewHas('customers');
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    });

    it('returns customer statistics', function () {
        $customer = Customer::factory()->create();
        Order::factory()->count(3)->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($this->user)->get('/customers/statistics');
        
        $response->assertStatus(200);
        $response->assertViewIs('customers.statistics');
    });

    it('handles customer search via API', function () {
        $customer = Customer::factory()->create(['name' => 'John Doe', 'phone' => '1234567890']);

        $response = $this->actingAs($this->user)
            ->get('/api/customers?q=John');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'customers' => [
                '*' => [
                    'id',
                    'name',
                    'phone',
                    'orders_qty',
                ]
            ]
        ]);
    });

    it('creates customer via API', function () {
        $customerData = [
            'name' => 'API Customer',
            'phone' => '5551234567',
        ];

        $response = $this->actingAs($this->user)
            ->post('/api/customers', $customerData);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('customers', $customerData);
    });

    it('handles duplicate phone numbers correctly', function () {
        $existingCustomer = Customer::factory()->create(['phone' => '1234567890']);
        
        $newCustomerData = [
            'name' => 'Another Customer',
            'phone' => '1234567890',
        ];

        $response = $this->actingAs($this->user)
            ->post('/customers', $newCustomerData);
        
        $response->assertSessionHasErrors(['phone']);
    });

    it('displays customer details page', function () {
        $customer = Customer::factory()->create();
        Order::factory()->count(2)->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($this->user)
            ->get("/customers/{$customer->id}");
        
        $response->assertStatus(200);
        $response->assertViewIs('customers.show');
        $response->assertSee($customer->name);
    });

    it('handles customer orders history', function () {
        $customer = Customer::factory()->create();
        $orders = Order::factory()->count(3)->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($this->user)
            ->get("/customers/{$customer->id}/orders");
        
        $response->assertStatus(200);
        $response->assertViewIs('customers.orders');
        $response->assertViewHas('orders');
    });

    it('updates customer orders quantity', function () {
        $customer = Customer::factory()->create(['orders_qty' => 0]);
        
        $customer->incrementOrdersQty();
        
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'orders_qty' => 1
        ]);
    });

    it('finds or creates customer by phone', function () {
        $phone = '1234567890';
        $name = 'John Doe';
        
        // First call should create
        $customer1 = Customer::findOrCreateByPhone($phone, $name);
        $this->assertDatabaseHas('customers', [
            'phone' => $phone,
            'name' => $name
        ]);
        
        // Second call should find existing
        $customer2 = Customer::findOrCreateByPhone($phone, 'Different Name');
        $this->assertEquals($customer1->id, $customer2->id);
        $this->assertEquals($name, $customer2->name); // Should keep original name
    });
}); 