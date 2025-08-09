<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'full_portion_price',
        'half_portion_price',
        'has_half_portion',
        'full_portion_name',
        'half_portion_name',
        'is_available',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'full_portion_price' => 'decimal:2',
        'half_portion_price' => 'decimal:2',
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

        // Unified pricing: orderType is ignored at item level; dine-in surcharge is applied to the order total
        if ($portion === 'half' && $this->half_portion_price !== null) {
            return (float) $this->half_portion_price;
        }

        if ($this->full_portion_price !== null) {
            return (float) $this->full_portion_price;
        }

        return (float) $this->price;
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
