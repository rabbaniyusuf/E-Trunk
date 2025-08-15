<?php

namespace App\Models;

use App\Models\Bin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WasteBinType extends Model
{
    use HasFactory;

    const STATUS_OK = 'OK';
    const STATUS_FULL = 'FULL';

    protected $fillable = ['bin_id', 'type', 'current_percentage', 'last_sensor_reading', 'status'];

    protected function casts(): array
    {
        return [
            'current_percentage' => 'decimal:2',
            'last_sensor_reading' => 'datetime',
        ];
    }

    // Relationships
    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }

    public function sensorReadings()
    {
        return $this->hasMany(SensorReadings::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransactions::class);
    }

    // Scopes
    public function scopeRecycle($query)
    {
        return $query->where('type', 'recycle');
    }

    public function scopeNonRecycle($query)
    {
        return $query->where('type', 'non_recycle');
    }

    public function scopeFull($query, $threshold = 80)
    {
        return $query->where('current_percentage', '>=', $threshold);
    }

    // Helper methods
    public function updateFromSensor($heightCm)
    {
        $percentage = ($heightCm / $this->max_height_cm) * 100;

        $this->update([
            'current_percentage' => min($percentage, 100),
            'last_sensor_reading' => now(),
        ]);

        return $this;
    }
}
