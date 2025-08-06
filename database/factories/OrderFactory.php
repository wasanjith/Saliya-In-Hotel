<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($this->faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'order_type' => $this->faker->randomElement(['dine_in', 'takeaway', 'delivery']),
            'customer_id' => Customer::factory(),
            'table_number' => $this->faker->optional()->numberBetween(1, 20),
            'customer_name' => $this->faker->name(),
            'customer_phone' => $this->faker->phoneNumber(),
            'subtotal' => $this->faker->randomFloat(2, 10, 200),
            'total_amount' => $this->faker->randomFloat(2, 10, 200),
            'customer_paid' => $this->faker->optional()->randomFloat(2, 10, 200),
            'balance_returned' => $this->faker->optional()->randomFloat(2, 0, 50),
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'gift', 'other']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
            'completed_at' => $this->faker->optional()->dateTime(),
        ];
    }
} 