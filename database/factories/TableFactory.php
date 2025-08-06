<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Table>
 */
class TableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numberBetween(1, 50),
            'capacity' => $this->faker->randomElement([2, 4, 6, 8, 10]),
            'status' => $this->faker->randomElement(['available', 'occupied', 'reserved']),
            'description' => $this->faker->optional()->sentence(),
            'location' => $this->faker->optional()->randomElement(['indoor', 'outdoor', 'window', 'corner']),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
} 