<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;



class InventoryCategory extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'is_active',
		'sort_order',
	];

	protected $casts = [
		'is_active' => 'boolean',
	];

	public function items(): HasMany
	{
		return $this->hasMany(InventoryItem::class, 'category_id');
	}
}


