<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FoodItem;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
	use RefreshDatabase;

	public function test_inventory_deducts_on_order(): void
	{
		$category = Category::factory()->create();
		$food = FoodItem::factory()->create(['category_id' => $category->id, 'full_price' => 1000, 'has_half_portion' => true]);

		$invCat = InventoryCategory::create(['name' => 'Meat']);
		$chicken = InventoryItem::create(['category_id' => $invCat->id, 'name' => 'Chicken', 'unit' => 'g', 'quantity' => 1000]);
		$riceCat = InventoryCategory::create(['name' => 'Rice']);
		$rice = InventoryItem::create(['category_id' => $riceCat->id, 'name' => 'Rice', 'unit' => 'g', 'quantity' => 2000]);

		$recipe = Recipe::create(['food_item_id' => $food->id, 'name' => 'Default']);
		RecipeItem::create(['recipe_id' => $recipe->id, 'inventory_item_id' => $chicken->id, 'quantity' => 200]);
		RecipeItem::create(['recipe_id' => $recipe->id, 'inventory_item_id' => $rice->id, 'quantity' => 400]);

        // Authenticate as a cashier to access protected POS routes
        $user = \App\Models\User::factory()->create(['job_role' => 'cashier']);
        $response = $this->actingAs($user)->postJson('/pos/order', [
			'order_type' => 'takeaway',
			'payment_method' => 'cash',
			'items' => [
				['food_item_id' => $food->id, 'quantity' => 1, 'portion' => 'full'],
			],
		]);

		$response->assertStatus(200);
		$chicken->refresh();
		$rice->refresh();

		$this->assertEquals(800.0, (float) $chicken->quantity);
		$this->assertEquals(1600.0, (float) $rice->quantity);
	}
}


