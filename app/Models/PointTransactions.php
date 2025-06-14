<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointTransactions extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'waste_bin_type_id', 'transaction_type', 'points', 'percentage_deposited', 'description', 'status', 'processed_by', 'processed_at'];

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
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}