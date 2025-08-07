<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_number',
        'status',
        'order_id',
        'occupied_at',
        'completed_at',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'occupied_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the order associated with this slot
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if slot is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Check if slot is occupied
     */
    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    /**
     * Check if slot is in maintenance
     */
    public function isInMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Get slot status text
     */
    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get slot display name
     */
    public function getDisplayNameAttribute(): string
    {
        return "Kitchen Slot {$this->slot_number}";
    }
}
