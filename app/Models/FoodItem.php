<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;



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
        'full_price', // New column
        'half_price', // New column
        'full_basmathi_price',
        'half_basmathi_price',
        'full_samba_price',
        'half_samba_price',
        'has_half_portion',
        'is_available',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'full_price' => 'decimal:2', // New column
        'half_price' => 'decimal:2', // New column
        'full_basmathi_price' => 'decimal:2',
        'half_basmathi_price' => 'decimal:2',
        'full_samba_price' => 'decimal:2',
        'half_samba_price' => 'decimal:2',
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
                $foodItem->slug = Str::slug($foodItem->name);
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
        // Use the new specific portion price fields if available
        if ($portion === 'half' && $this->half_price !== null) {
            return (float) $this->half_price;
        }
        
        if ($portion === 'full' && $this->full_price !== null) {
            return (float) $this->full_price;
        }
        
        // Fall back to the base price
        return (float) $this->price;
    }

    /**
     * Get the portion name for a specific portion
     */
    public function getPortionName(string $portion = 'full'): string
    {
        return $portion === 'half' ? 'Half Portion' : 'Full Portion';
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

    /**
     * Whether the item belongs to the 'Fried Rice' category
     */
    public function isFriedRice(): bool
    {
        // Try loaded relation first to avoid extra query
        if ($this->relationLoaded('category') && $this->category) {
            return $this->category->name === 'Fried Rice';
        }

        // Fallback to querying relation when not loaded
        $category = $this->category()->first();
        return $category ? $category->name === 'Fried Rice' : false;
    }

    /**
     * Get price for a given rice type if applicable (samba|basmathi)
     */
    public function getRicePrice(?string $riceType, string $portion = 'full'): ?float
    {
        if (!$riceType) {
            return null;
        }

        $riceType = strtolower($riceType);
        $isHalf = $portion === 'half';

        if ($riceType === 'samba') {
            if ($isHalf && $this->half_samba_price !== null) {
                return (float) $this->half_samba_price;
            }
            if (!$isHalf && $this->full_samba_price !== null) {
                return (float) $this->full_samba_price;
            }
        }

        if ($riceType === 'basmathi' || $riceType === 'basmati') {
            if ($isHalf && $this->half_basmathi_price !== null) {
                return (float) $this->half_basmathi_price;
            }
            if (!$isHalf && $this->full_basmathi_price !== null) {
                return (float) $this->full_basmathi_price;
            }
        }
        return null;
    }

    /**
     * Get price considering rice type when relevant; falls back to portion pricing
     */
    public function getPriceWithRiceType(string $portion = 'full', ?string $riceType = null, string $orderType = 'dine_in'): float
    {
        if ($this->isFriedRice()) {
            // Respect portion for rice-type prices
            if ($portion === 'half' && !$this->has_half_portion) {
                $portion = 'full';
            }
            $ricePrice = $this->getRicePrice($riceType, $portion);
            if ($ricePrice !== null) {
                return $ricePrice;
            }
        }
        return $this->getPrice($portion, $orderType);
    }
}
