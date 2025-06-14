<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WasteBinType extends Model
{
    use HasFactory;

    protected $fillable = ['waste_bin_id', 'type', 'current_height_cm', 'max_height_cm', 'current_percentage', 'last_sensor_reading'];

    protected function casts(): array
    {
        return [
            'current_height_cm' => 'decimal:2',
            'max_height_cm' => 'decimal:2',
            'current_percentage' => 'decimal:2',
            'last_sensor_reading' => 'datetime',
        ];
    }

    // Relationships
    public function wasteBin()
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
            'current_height_cm' => $heightCm,
            'current_percentage' => min($percentage, 100),
            'last_sensor_reading' => now(),
        ]);

        return $this;
    }

    public function getStatusLevel()
    {
        if ($this->current_percentage >= 80) {
            return 'FULL';
        }
        if ($this->current_percentage >= 60) {
            return 'HIGH';
        }
        if ($this->current_percentage >= 30) {
            return 'MEDIUM';
        }
        return 'LOW';
    }
}