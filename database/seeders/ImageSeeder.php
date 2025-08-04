<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\FoodItem;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update categories with images
        $categoryImages = [
            'Fried Rice' => 'categories/fried-rice.png',
            'Kottu' => 'categories/kottu.png',
            'Bites' => 'categories/bites.png',
            'Dewals' => 'categories/dewals.png',
            'Drinks' => 'categories/drinks.png',
            'Desserts' => 'categories/desserts.png',
        ];

        foreach ($categoryImages as $categoryName => $imagePath) {
            $category = Category::where('name', $categoryName)->first();
            if ($category) {
                $category->update(['image' => $imagePath]);
                echo "Updated category: {$categoryName} with image: {$imagePath}\n";
            }
        }

        // Update food items with images
        $foodItemImages = [
            'Chicken Fried Rice' => 'food-items/chicken-fried-rice.png',
            'Egg Fried Rice' => 'food-items/egg-fried-rice.png',
            'Vegetable Fried Rice' => 'food-items/vegetable-fried-rice.png',
            'Prawn Fried Rice' => 'food-items/prawn-fried-rice.png',
            'Chicken Kottu' => 'food-items/chicken-kottu.png',
            'Egg Kottu' => 'food-items/egg-kottu.png',
            'Chicken Wings' => 'food-items/chicken-wings.png',
            'Spring Rolls' => 'food-items/spring-rolls.png',
            'Chicken Curry' => 'food-items/chicken-curry.png',
            'Fish Curry' => 'food-items/fish-curry.png',
            'Coca Cola' => 'food-items/coca-cola.png',
            'Fresh Lime Juice' => 'food-items/lime-juice.png',
            'Ice Cream' => 'food-items/ice-cream.png',
            'Chocolate Cake' => 'food-items/chocolate-cake.png',
        ];

        foreach ($foodItemImages as $itemName => $imagePath) {
            $foodItem = FoodItem::where('name', $itemName)->first();
            if ($foodItem) {
                $foodItem->update(['image' => $imagePath]);
                echo "Updated food item: {$itemName} with image: {$imagePath}\n";
            }
        }

        echo "\n✅ Images assigned successfully!\n";
        echo "🌐 You can now view the POS system with images at: http://localhost:8000/pos\n";
    }
}
