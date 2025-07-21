<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    const TYPE_COLLECTION_REQUEST = 'collection_request';
    const TYPE_POINT_TRANSACTION = 'point_transaction';
    const TYPE_SCHEDULE_UPDATE = 'schedule_update';
    const TYPE_SYSTEM = 'system';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'notifiable_type',
        'notifiable_id',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }

        return $this;
    }

    public function isUnread()
    {
        return is_null($this->read_at);
    }

    public function getIconClass()
    {
        return match($this->type) {
            self::TYPE_COLLECTION_REQUEST => 'bi bi-recycle',
            self::TYPE_POINT_TRANSACTION => 'bi bi-coin',
            self::TYPE_SCHEDULE_UPDATE => 'bi bi-calendar-event',
            self::TYPE_SYSTEM => 'bi bi-info-circle',
            default => 'bi bi-bell'
        };
    }

    public function getTypeColor()
    {
        return match($this->type) {
            self::TYPE_COLLECTION_REQUEST => 'success',
            self::TYPE_POINT_TRANSACTION => 'warning',
            self::TYPE_SCHEDULE_UPDATE => 'info',
            self::TYPE_SYSTEM => 'primary',
            default => 'secondary'
        };
    }
}
