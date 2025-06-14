<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key_name', 'value', 'description', 'type'];

    // Helper methods
    public static function get($key, $default = null)
    {
        $setting = static::where('key_name', $key)->first();

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    public static function set($key, $value, $type = 'string')
    {
        return static::updateOrCreate(
            ['key_name' => $key],
            [
                'value' => $value,
                'type' => $type,
            ],
        );
    }

    private static function castValue($value, $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'boolean' => (bool) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
