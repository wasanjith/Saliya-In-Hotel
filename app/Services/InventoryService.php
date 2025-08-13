<?php

namespace App\Services;

use App\Models\FoodItem;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Log;

class InventoryService
{
	/**
	 * Deduct inventory based on an order's items array (JSON structure stored on OrderItem)
	 * Each array element should include: food_item_id, quantity, portion
	 */
	public function deductForOrderItems(array $items): void
	{
		foreach ($items as $line) {
			if (!isset($line['food_item_id']) || !isset($line['quantity'])) {
				continue;
			}

			$foodItem = FoodItem::with(['recipe.items.inventoryItem'])->find($line['food_item_id']);
			if (!$foodItem || !$foodItem->recipe) {
				Log::warning('No recipe found for food item during inventory deduction', [
					'food_item_id' => $line['food_item_id'],
				]);
				continue;
			}

			$quantityOrdered = (int) ($line['quantity'] ?? 1);
			$portion = $line['portion'] ?? 'full';
			$portionMultiplier = $portion === 'half' ? 0.5 : 1.0;

			foreach ($foodItem->recipe->items as $recipeItem) {
				$inventoryItem = $recipeItem->inventoryItem;
				if (!$inventoryItem) {
					continue;
				}

				$deductQuantity = (float) $recipeItem->quantity * $portionMultiplier * $quantityOrdered;
				$original = (float) $inventoryItem->quantity;
				$inventoryItem->quantity = $original - $deductQuantity;
				$inventoryItem->save();

				if ($inventoryItem->quantity < 0) {
					Log::warning('Inventory went below zero', [
						'inventory_item_id' => $inventoryItem->id,
						'name' => $inventoryItem->name,
						'final_quantity' => (float) $inventoryItem->quantity,
					]);
				}
			}
		}
	}
}


