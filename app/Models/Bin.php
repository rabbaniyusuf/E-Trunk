<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    protected $fillable = [
        'user_id',
        'bin_code',
        'type',
        'status',
        'current_weight',
        'capacity',
        'last_pickup',
        'notes',
    ];

     protected function casts(): array
    {
        return [
            'current_weight' => 'decimal:2',
            'capacity' => 'decimal:2',
            'last_pickup' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the bin.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if bin is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if bin is full.
     */
    public function isFull()
    {
        return $this->current_weight >= $this->capacity;
    }

    /**
     * Get fill percentage.
     */
    public function getFillPercentageAttribute()
    {
        return round(($this->current_weight / $this->capacity) * 100, 2);
    }

    /**
     * Get remaining capacity.
     */
    public function getRemainingCapacityAttribute()
    {
        return $this->capacity - $this->current_weight;
    }

    /**
     * Generate unique bin code.
     */
    public static function generateBinCode($userId, $type)
    {
        $prefix = $type === 'recycle' ? 'RC' : 'NR';
        return $prefix . str_pad($userId, 4, '0', STR_PAD_LEFT) . rand(100, 999);
    }
}
