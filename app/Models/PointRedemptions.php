<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointRedemptions extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points_redeemed',
        'cash_value',
        'redemption_type',
        'status',
        'notes',
        'processed_by',
        'processed_at',
        'completed_at',
        'redemption_code'
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'points_redeemed' => 'integer',
            'cash_value' => 'decimal:2',
            'processed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

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
        return $this->hasOne(Schedules::class);
    }

    // Scopes
    public function scopeCash($query)
    {
        return $query->where('redemption_type', 'cash');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Helper methods
    public static function calculateCashValue($points)
    {
        // 10 poin = Rp 1000
        return ($points / 10) * 1000;
    }

    public static function getValidPointsOptions()
    {
        return [20, 40, 60, 80, 100];
    }

    public function generateRedemptionCode()
    {
        return 'RDM-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_COMPLETED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            self::STATUS_PENDING => 'Menunggu Penukaran',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];

        return $texts[$this->status] ?? 'Unknown';
    }

    // Boot method to generate redemption code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($redemption) {
            if (empty($redemption->redemption_code)) {
                $redemption->redemption_code = $redemption->generateRedemptionCode();
            }
        });
    }
}
