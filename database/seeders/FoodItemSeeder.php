<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FoodItem;
use App\Models\Category;
use Illuminate\Support\Str;

class FoodItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs
        $friedRiceCategory = Category::where('name', 'Fried Rice')->first();
        $kottuCategory = Category::where('name', 'Kottu')->first();
        $bitesCategory = Category::where('name', 'Bites')->first();
        $dewalsCategory = Category::where('name', 'Dewals')->first();
        $drinksCategory = Category::where('name', 'Drinks')->first();
        $dessertsCategory = Category::where('name', 'Desserts')->first();

        $foodItems = [
            // Fried Rice Items
            [
                'category_id' => $friedRiceCategory->id,
                'name' => 'Chicken Fried Rice',
                'description' => 'Delicious fried rice with tender chicken pieces',
                'dine_in_price' => 12.99,
                'takeaway_price' => 11.99,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $friedRiceCategory->id,
                'name' => 'Egg Fried Rice',
                'description' => 'Classic fried rice with scrambled eggs',
                'dine_in_price' => 10.99,
                'takeaway_price' => 9.99,
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'category_id' => $friedRiceCategory->id,
                'name' => 'Vegetable Fried Rice',
                'description' => 'Healthy fried rice with fresh vegetables',
                'dine_in_price' => 9.99,
                'takeaway_price' => 8.99,
                'is_featured' => false,
                'sort_order' => 3,
            ],
            [
                'category_id' => $friedRiceCategory->id,
                'name' => 'Prawn Fried Rice',
                'description' => 'Premium fried rice with succulent prawns',
                'dine_in_price' => 15.99,
                'takeaway_price' => 14.99,
                'is_featured' => true,
                'sort_order' => 4,
            ],

            // Kottu Items
            [
                'category_id' => $kottuCategory->id,
                'name' => 'Chicken Kottu',
                'description' => 'Traditional kottu with chicken and vegetables',
                'dine_in_price' => 13.99,
                'takeaway_price' => 12.99,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $kottuCategory->id,
                'name' => 'Egg Kottu',
                'description' => 'Kottu with scrambled eggs and vegetables',
                'dine_in_price' => 11.99,
                'takeaway_price' => 10.99,
                'is_featured' => false,
                'sort_order' => 2,
            ],

            // Bites Items
            [
                'category_id' => $bitesCategory->id,
                'name' => 'Chicken Wings',
                'description' => 'Crispy fried chicken wings with sauce',
                'dine_in_price' => 8.99,
                'takeaway_price' => 7.99,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $bitesCategory->id,
                'name' => 'Spring Rolls',
                'description' => 'Crispy vegetable spring rolls',
                'dine_in_price' => 6.99,
                'takeaway_price' => 5.99,
                'is_featured' => false,
                'sort_order' => 2,
            ],

            // Dewals Items
            [
                'category_id' => $dewalsCategory->id,
                'name' => 'Chicken Curry',
                'description' => 'Traditional chicken curry with spices',
                'dine_in_price' => 14.99,
                'takeaway_price' => 13.99,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $dewalsCategory->id,
                'name' => 'Fish Curry',
                'description' => 'Spicy fish curry with coconut milk',
                'dine_in_price' => 16.99,
                'takeaway_price' => 15.99,
                'is_featured' => false,
                'sort_order' => 2,
            ],

            // Drinks
            [
                'category_id' => $drinksCategory->id,
                'name' => 'Coca Cola',
                'description' => 'Refreshing Coca Cola',
                'dine_in_price' => 2.99,
                'takeaway_price' => 2.49,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'category_id' => $drinksCategory->id,
                'name' => 'Fresh Lime Juice',
                'description' => 'Fresh squeezed lime juice',
                'dine_in_price' => 3.99,
                'takeaway_price' => 3.49,
                'is_featured' => true,
                'sort_order' => 2,
            ],

            // Desserts
            [
                'category_id' => $dessertsCategory->id,
                'name' => 'Ice Cream',
                'description' => 'Vanilla ice cream with toppings',
                'dine_in_price' => 4.99,
                'takeaway_price' => 4.49,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'category_id' => $dessertsCategory->id,
                'name' => 'Chocolate Cake',
                'description' => 'Rich chocolate cake slice',
                'dine_in_price' => 5.99,
                'takeaway_price' => 5.49,
                'is_featured' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($foodItems as $item) {
            FoodItem::create([
                'category_id' => $item['category_id'],
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'dine_in_price' => $item['dine_in_price'],
                'takeaway_price' => $item['takeaway_price'],
                'is_available' => true,
                'is_featured' => $item['is_featured'],
                'sort_order' => $item['sort_order'],
            ]);
        }
    }
}
