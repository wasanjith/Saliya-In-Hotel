<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FoodItem>
 */
class FoodItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dineInPrice = $this->faker->randomFloat(2, 5, 50);
        $takeawayPrice = $dineInPrice + $this->faker->randomFloat(2, 1, 5);
        
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'image' => null,
            'dine_in_price' => $dineInPrice,
            'takeaway_price' => $takeawayPrice,
            'full_portion_dine_in_price' => $dineInPrice,
            'full_portion_takeaway_price' => $takeawayPrice,
            'half_portion_dine_in_price' => $dineInPrice * 0.6,
            'half_portion_takeaway_price' => $takeawayPrice * 0.6,
            'has_half_portion' => $this->faker->boolean(30),
            'full_portion_name' => 'Full Portion',
            'half_portion_name' => 'Half Portion',
            'is_available' => true,
            'is_featured' => $this->faker->boolean(20),
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
} 