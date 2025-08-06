<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\FoodItem;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'items',
        'total_amount',
    ];

    protected $casts = [
        'items' => 'array',
        'total_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get all food items referenced in this order item
     */
    public function foodItems()
    {
        $foodItemIds = collect($this->items)->pluck('food_item_id')->unique();
        return FoodItem::whereIn('id', $foodItemIds)->get();
    }

    /**
     * Add a food item to the items array with portion support
     */
    public function addFoodItem(int $foodItemId, string $itemName, int $quantity, float $unitPrice, string $portion = 'full', ?string $notes = null): void
    {
        $items = $this->items ?? [];
        
        $totalPrice = $quantity * $unitPrice;
        
        $items[] = [
            'food_item_id' => $foodItemId,
            'item_name' => $itemName,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'portion' => $portion,
            'notes' => $notes,
        ];
        
        $this->items = $items;
        $this->calculateTotalAmount();
    }

    /**
     * Add a food item with automatic price calculation based on portion and order type
     */
    public function addFoodItemWithPortion(int $foodItemId, int $quantity, string $portion = 'full', string $orderType = 'dine_in', ?string $notes = null): void
    {
        $foodItem = FoodItem::find($foodItemId);
        
        if (!$foodItem) {
            throw new \Exception("Food item not found with ID: {$foodItemId}");
        }

        $unitPrice = $foodItem->getPrice($portion, $orderType);
        $itemName = $foodItem->name . ' (' . $foodItem->getPortionName($portion) . ')';
        
        $this->addFoodItem($foodItemId, $itemName, $quantity, $unitPrice, $portion, $notes);
    }

    /**
     * Calculate and update the total amount for all items
     */
    public function calculateTotalAmount(): void
    {
        $total = collect($this->items)->sum('total_price');
        $this->total_amount = $total;
    }

    /**
     * Get the total quantity of all items
     */
    public function getTotalQuantity(): int
    {
        return collect($this->items)->sum('quantity');
    }

    /**
     * Get items count
     */
    public function getItemsCount(): int
    {
        return count($this->items ?? []);
    }

    /**
     * Get items grouped by portion
     */
    public function getItemsByPortion(): array
    {
        $grouped = [];
        
        foreach ($this->items ?? [] as $item) {
            $portion = $item['portion'] ?? 'full';
            if (!isset($grouped[$portion])) {
                $grouped[$portion] = [];
            }
            $grouped[$portion][] = $item;
        }
        
        return $grouped;
    }
}
