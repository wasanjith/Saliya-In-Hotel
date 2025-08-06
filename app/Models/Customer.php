<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'orders_qty',
    ];

    protected $casts = [
        'orders_qty' => 'integer',
    ];

    /**
     * Get the orders for this customer
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Increment the orders quantity
     */
    public function incrementOrdersQty(): void
    {
        $this->increment('orders_qty');
    }

    /**
     * Find or create customer by phone number
     */
    public static function findOrCreateByPhone(string $phone, string $name = null): self
    {
        $customer = static::where('phone', $phone)->first();
        
        if (!$customer) {
            $customer = static::create([
                'name' => $name,
                'phone' => $phone,
                'orders_qty' => 0,
            ]);
        }
        
        return $customer;
    }
}
