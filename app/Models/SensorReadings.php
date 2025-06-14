<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SensorReadings extends Model
{
    use HasFactory;

    protected $fillable = ['waste_bin_type_id', 'height_cm', 'percentage', 'temperature', 'humidity', 'reading_time'];

    protected function casts(): array
    {
        return [
            'height_cm' => 'decimal:2',
            'percentage' => 'decimal:2',
            'temperature' => 'decimal:2',
            'humidity' => 'decimal:2',
            'reading_time' => 'datetime',
        ];
    }

    // Relationships
    public function wasteBinType()
    {
        return $this->belongsTo(WasteBinType::class);
    }

    // Scopes
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('reading_time', '>=', now()->subHours($hours));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('reading_time', today());
    }
}