<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointRedemptions extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'points_redeemed', 'cash_value', 'redemption_type', 'status', 'notes', 'processed_by', 'processed_at', 'completed_at'];

    protected function casts(): array
    {
        return [
            'points_redeemed' => 'integer',
            'cash_value' => 'decimal:2',
            'processed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

    // Scopes
    public function scopeCash($query)
    {
        return $query->where('redemption_type', 'cash');
    }

    public function scopeDonation($query)
    {
        return $query->where('redemption_type', 'donation');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public static function calculateCashValue($points)
    {
        // 20 poin = Rp 1000
        return ($points / 20) * 1000;
    }

    public static function getValidPointsOptions()
    {
        return [20, 40, 60, 80, 100];
    }
}