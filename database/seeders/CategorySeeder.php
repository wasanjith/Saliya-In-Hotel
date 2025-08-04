<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fried Rice',
                'description' => 'Delicious fried rice dishes with various ingredients',
                'sort_order' => 1,
            ],
            [
                'name' => 'Kottu',
                'description' => 'Traditional Sri Lankan kottu roti dishes',
                'sort_order' => 2,
            ],
            [
                'name' => 'Bites',
                'description' => 'Small appetizers and snacks',
                'sort_order' => 3,
            ],
            [
                'name' => 'Dewals',
                'description' => 'Traditional curry dishes',
                'sort_order' => 4,
            ],
            [
                'name' => 'Drinks',
                'description' => 'Beverages and refreshments',
                'sort_order' => 5,
            ],
            [
                'name' => 'Desserts',
                'description' => 'Sweet treats and desserts',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'is_active' => true,
                'sort_order' => $category['sort_order'],
            ]);
        }
    }
}
