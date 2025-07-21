<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WasteCollection extends Model
{
     use HasFactory;

    const STATUS_PENDING = 'MENUNGGU_JADWAL';
    const STATUS_SCHEDULED = 'TERJADWAL';
    const STATUS_IN_PROGRESS = 'SEDANG_DIPROSES';
    const STATUS_COMPLETED = 'SELESAI';
    const STATUS_CANCELLED = 'DIBATALKAN';

    protected $fillable = [
        'user_id',
        'waste_bin_type_id',
        'waste_types',
        'pickup_date',
        'pickup_time',
        'status',
        'notes',
        'assigned_to',
        'processed_by',
        'processed_at',
        'completed_at',
    ];

    protected $casts = [
        'waste_types' => 'array',
        'pickup_date' => 'date',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wasteBinType()
    {
        return $this->belongsTo(WasteBinType::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransactions::class, 'collection_request_id');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Helper methods
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_SCHEDULED => 'bg-info',
            self::STATUS_IN_PROGRESS => 'bg-primary',
            self::STATUS_COMPLETED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu Jadwal',
            self::STATUS_SCHEDULED => 'Terjadwal',
            self::STATUS_IN_PROGRESS => 'Sedang Diproses',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Tidak Diketahui'
        };
    }

    public function getWasteTypesLabel()
    {
        if (empty($this->waste_types)) {
            return 'Tidak ada';
        }

        $labels = [
            'kardus' => 'Kardus',
            'plastik' => 'Plastik',
            'kertas' => 'Kertas'
        ];

        return collect($this->waste_types)
            ->map(fn($type) => $labels[$type] ?? ucfirst($type))
            ->join(', ');
    }
}