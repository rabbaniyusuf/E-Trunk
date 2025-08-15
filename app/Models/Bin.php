<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    use HasFactory;

    protected $fillable = [
        'bin_code',
        'location',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

     public function users()
    {
        return $this->hasMany(User::class, 'waste_bin_code', 'bin_code');
    }

    public function wasteBinTypes()
    {
        return $this->hasMany(WasteBinType::class, 'bin_id', 'id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedules::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function getRecycleBin()
    {
        return $this->wasteBinTypes()->where('type', 'recycle')->first();
    }

    public function getNonRecycleBin()
    {
        return $this->wasteBinTypes()->where('type', 'non_recycle')->first();
    }

    public function getAveragePercentage()
    {
        return $this->wasteBinTypes()->avg('current_percentage');
    }

    public function isFull($threshold = 80)
    {
        return $this->getAveragePercentage() >= $threshold;
    }
}