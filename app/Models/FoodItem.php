<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodItem extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'dine_in_price',
        'takeaway_price',
        'full_portion_dine_in_price',
        'full_portion_takeaway_price',
        'half_portion_dine_in_price',
        'half_portion_takeaway_price',
        'has_half_portion',
        'full_portion_name',
        'half_portion_name',
        'is_available',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'dine_in_price' => 'decimal:2',
        'takeaway_price' => 'decimal:2',
        'full_portion_dine_in_price' => 'decimal:2',
        'full_portion_takeaway_price' => 'decimal:2',
        'half_portion_dine_in_price' => 'decimal:2',
        'half_portion_takeaway_price' => 'decimal:2',
        'has_half_portion' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($foodItem) {
            // Auto-generate slug if not provided
            if (empty($foodItem->slug)) {
                $foodItem->slug = \Str::slug($foodItem->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the image URL attribute
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/placeholder-food.svg');
    }

    /**
     * Get the price for a specific portion and order type
     */
    public function getPrice(string $portion = 'full', string $orderType = 'dine_in'): float
    {
        if ($portion === 'half' && !$this->has_half_portion) {
            // If half portion is not available, return full portion price
            $portion = 'full';
        }

        $priceField = "{$portion}_portion_{$orderType}_price";
        
        if (isset($this->$priceField) && $this->$priceField !== null) {
            return (float) $this->$priceField;
        }

        // Fallback to original price fields if portion-specific prices are not set
        if ($orderType === 'dine_in') {
            return (float) $this->dine_in_price;
        } else {
            return (float) $this->takeaway_price;
        }
    }

    /**
     * Get the portion name for a specific portion
     */
    public function getPortionName(string $portion = 'full'): string
    {
        if ($portion === 'half') {
            return $this->half_portion_name ?? 'Half Portion';
        }
        
        return $this->full_portion_name ?? 'Full Portion';
    }

    /**
     * Check if half portion is available
     */
    public function hasHalfPortion(): bool
    {
        return $this->has_half_portion;
    }

    /**
     * Get all available portions for this food item
     */
    public function getAvailablePortions(): array
    {
        $portions = ['full'];
        
        if ($this->has_half_portion) {
            $portions[] = 'half';
        }
        
        return $portions;
    }

    /**
     * Get portion options with prices for a specific order type
     */
    public function getPortionOptions(string $orderType = 'dine_in'): array
    {
        $options = [];
        
        // Always include full portion
        $options['full'] = [
            'name' => $this->getPortionName('full'),
            'price' => $this->getPrice('full', $orderType),
        ];
        
        // Include half portion if available
        if ($this->has_half_portion) {
            $options['half'] = [
                'name' => $this->getPortionName('half'),
                'price' => $this->getPrice('half', $orderType),
            ];
        }
        
        return $options;
    }
}
