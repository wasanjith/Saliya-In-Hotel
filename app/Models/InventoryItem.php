<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;



class InventoryItem extends Model
{
	use HasFactory;

	protected $fillable = [
		'category_id',
		'name',
		'unit',
		'quantity',
		'reorder_level',
		'is_active',
	];

	protected $casts = [
		'quantity' => 'decimal:3',
		'reorder_level' => 'decimal:3',
		'is_active' => 'boolean',
	];

	public function category(): BelongsTo
	{
		return $this->belongsTo(InventoryCategory::class, 'category_id');
	}

	public function recipeLines(): HasMany
	{
		return $this->hasMany(RecipeItem::class, 'inventory_item_id');
	}
}


