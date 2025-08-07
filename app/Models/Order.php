<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_type',
        'customer_id',
        'table_number',
        'customer_name',
        'customer_phone',
        'subtotal',
        'total_amount',
        'customer_paid',
        'balance_returned',
        'payment_method',
        'status',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'customer_paid' => 'decimal:2',
        'balance_returned' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the customer that owns the order
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the kitchen slot associated with this order
     */
    public function kitchenSlot(): BelongsTo
    {
        return $this->belongsTo(KitchenSlot::class);
    }


    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Generate a unique order number
     */
    private static function generateOrderNumber()
    {
        $datePrefix = 'ORD-' . date('Ymd') . '-';
        
        // Get the latest order number for today
        $latestOrder = static::where('order_number', 'like', $datePrefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();
        
        if ($latestOrder) {
            // Extract the sequence number from the latest order
            $latestNumber = str_replace($datePrefix, '', $latestOrder->order_number);
            $nextNumber = intval($latestNumber) + 1;
        } else {
            // First order for today
            $nextNumber = 1;
        }
        
        // Ensure uniqueness in case of race conditions
        $attempts = 0;
        do {
            $orderNumber = $datePrefix . str_pad($nextNumber + $attempts, 4, '0', STR_PAD_LEFT);
            $exists = static::where('order_number', $orderNumber)->exists();
            $attempts++;
        } while ($exists && $attempts < 100);
        
        return $orderNumber;
    }
}
