<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'status',
        'location',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationship with orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Check if table is available
    public function isAvailable()
    {
        return $this->status === 'available' && $this->is_active;
    }

    // Check if table is occupied
    public function isOccupied()
    {
        return $this->status === 'occupied';
    }

    // Check if table is reserved
    public function isReserved()
    {
        return $this->status === 'reserved';
    }

    // Check if table is under maintenance
    public function isUnderMaintenance()
    {
        return $this->status === 'maintenance';
    }

    // Check if table has an active order
    public function hasActiveOrder()
    {
        return $this->orders()
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->exists();
    }

    // Get current active order
    public function getCurrentOrder()
    {
        return $this->orders()
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->latest()
            ->first();
    }
}
