<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\FoodItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $foodItem = FoodItem::factory()->create();
        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $foodItem->dine_in_price;
        $totalPrice = $quantity * $unitPrice;
        
        return [
            'order_id' => Order::factory(),
            'items' => [
                [
                    'food_item_id' => $foodItem->id,
                    'item_name' => $foodItem->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'portion' => $this->faker->randomElement(['full', 'half']),
                    'notes' => $this->faker->optional()->sentence(),
                ]
            ],
            'total_amount' => $totalPrice,
        ];
    }
} 