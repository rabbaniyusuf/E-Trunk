<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedules extends Model
{
    use HasFactory;

    protected $fillable = ['petugas_id', 'user_id', 'bin_id', 'point_redemption_id', 'schedule_type', 'scheduled_date', 'scheduled_time', 'priority', 'status', 'notes', 'completed_at'];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'scheduled_time' => 'datetime:H:i',
            'completed_at' => 'datetime',
        ];
    }

    // Relationships
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wasteBin()
    {
        return $this->belongsTo(Bin::class);
    }

    public function pointRedemption()
    {
        return $this->belongsTo(PointRedemptions::class);
    }

    // Scopes
    public function scopeWasteCollection($query)
    {
        return $query->where('schedule_type', 'waste_collection');
    }

    public function scopeCashExchange($query)
    {
        return $query->where('schedule_type', 'cash_exchange');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', today())->whereIn('status', ['scheduled', 'in_progress']);
    }
}