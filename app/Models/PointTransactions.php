<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointTransactions extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'MENUNGGU_DIAMBIL';
    const STATUS_APPROVED = 'SUDAH_DIAMBIL';
    const STATUS_REJECTED = 'GAGAL_DIAMBIL';

    protected $fillable = [
        'user_id',
        'waste_collection_id',
        'transaction_type',
        'points',
        'percentage_deposited',
        'description',
        'status',
        'processed_by',
        'processed_at'
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'percentage_deposited' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wasteBinType()
    {
        return $this->belongsTo(WasteBinType::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function collectionRequest()
    {
        return $this->belongsTo(WasteCollection::class, 'collection_request_id');
    }

    // Scopes
    public function scopeDeposit($query)
    {
        return $query->where('transaction_type', 'deposit');
    }

    public function scopeWithdrawal($query)
    {
        return $query->where('transaction_type', 'withdrawal');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // Helper methods
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_APPROVED => 'bg-success',
            self::STATUS_REJECTED => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu Diambil',
            self::STATUS_APPROVED => 'Sudah Diambil',
            self::STATUS_REJECTED => 'Gagal Diambil',
            default => 'Tidak Diketahui'
        };
    }

    public function getTypeLabel()
    {
        return match($this->transaction_type) {
            'deposit' => 'Menabung',
            'withdrawal' => 'Penarikan',
            default => 'Tidak Diketahui'
        };
    }

    public function getTypeColor()
    {
        return match($this->transaction_type) {
            'deposit' => 'success',
            'withdrawal' => 'danger',
            default => 'secondary'
        };
    }
}
