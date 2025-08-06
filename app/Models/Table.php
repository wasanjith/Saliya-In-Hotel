<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'capacity',
        'status',
        'description',
        'location',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'sort_order' => 'integer'
    ];

    /**
     * Get the orders for this table
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_number', 'number');
    }

    /**
     * Get the current active order for this table
     */
    public function currentOrder()
    {
        return $this->orders()
            ->whereIn('status', ['confirmed', 'pending'])
            ->latest()
            ->first();
    }

    /**
     * Check if table is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Check if table is occupied
     */
    public function isOccupied(): bool
    {
        return $this->status === 'occupied' || $this->currentOrder() !== null;
    }

    /**
     * Check if table is reserved
     */
    public function isReserved(): bool
    {
        return $this->status === 'reserved';
    }

    /**
     * Get table status text
     */
    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get table display name
     */
    public function getDisplayNameAttribute(): string
    {
        return "Table {$this->number}";
    }
}
