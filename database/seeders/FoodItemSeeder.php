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
                'price' => 1199,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $friedRiceCategory->id,
                'name' => 'Egg Fried Rice',
                'description' => 'Classic fried rice with scrambled eggs',
                'price' => 999,
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'category_id' => $friedRiceCategory->id,
                'name' => 'Vegetable Fried Rice',
                'description' => 'Healthy fried rice with fresh vegetables',
                'price' => 899,
                'is_featured' => false,
                'sort_order' => 3,
            ],
            [
                'category_id' => $friedRiceCategory->id,
                'name' => 'Prawn Fried Rice',
                'description' => 'Premium fried rice with succulent prawns',
                'price' => 1499,
                'is_featured' => true,
                'sort_order' => 4,
            ],

            // Kottu Items
            [
                'category_id' => $kottuCategory->id,
                'name' => 'Chicken Kottu',
                'description' => 'Traditional kottu with chicken and vegetables',
                'price' => 1299,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $kottuCategory->id,
                'name' => 'Egg Kottu',
                'description' => 'Kottu with scrambled eggs and vegetables',
                'price' => 1099,
                'is_featured' => false,
                'sort_order' => 2,
            ],

            // Bites Items
            [
                'category_id' => $bitesCategory->id,
                'name' => 'Chicken Wings',
                'description' => 'Crispy fried chicken wings with sauce',
                'price' => 799,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $bitesCategory->id,
                'name' => 'Spring Rolls',
                'description' => 'Crispy vegetable spring rolls',
                'price' => 599,
                'is_featured' => false,
                'sort_order' => 2,
            ],

            // Dewals Items
            [
                'category_id' => $dewalsCategory->id,
                'name' => 'Chicken Curry',
                'description' => 'Traditional chicken curry with spices',
                'price' => 1399,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $dewalsCategory->id,
                'name' => 'Fish Curry',
                'description' => 'Spicy fish curry with coconut milk',
                'price' => 1599,
                'is_featured' => false,
                'sort_order' => 2,
            ],

            // Drinks
            [
                'category_id' => $drinksCategory->id,
                'name' => 'Coca Cola',
                'description' => 'Refreshing Coca Cola',
                'price' => 249,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'category_id' => $drinksCategory->id,
                'name' => 'Fresh Lime Juice',
                'description' => 'Fresh squeezed lime juice',
                'price' => 349,
                'is_featured' => true,
                'sort_order' => 2,
            ],

            // Desserts
            [
                'category_id' => $dessertsCategory->id,
                'name' => 'Ice Cream',
                'description' => 'Vanilla ice cream with toppings',
                'price' => 449,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'category_id' => $dessertsCategory->id,
                'name' => 'Chocolate Cake',
                'description' => 'Rich chocolate cake slice',
                'price' => 549,
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
                'price' => $item['price'],
                'is_available' => true,
                'is_featured' => $item['is_featured'],
                'sort_order' => $item['sort_order'],
            ]);
        }
    }
}
