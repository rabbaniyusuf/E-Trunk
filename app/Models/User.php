<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'phone', 'address', 'district', 'postal_code', 'balance', 'waste_bin_code'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
        ];
    }

    public function isPetugasPusat(): bool
    {
        return $this->hasRole('petugas_pusat');
    }

    public function isPetugasKebersihan(): bool
    {
        return $this->hasRole('petugas_kebersihan');
    }

    public function isMasyarakat(): bool
    {
        return $this->hasRole('masyarakat');
    }

     public function bins()
    {
        return $this->hasMany(Bin::class);
    }

    /**
     * Get the recycle bin for the user.
     */
     public function wasteBin()
    {
        return $this->belongsTo(Bin::class, 'waste_bin_code', 'bin_code');
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransactions::class);
    }

    public function pointRedemptions()
    {
        return $this->hasMany(PointRedemptions::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedules::class, 'petugas_id');
    }

    public function userSchedules()
    {
        return $this->hasMany(Schedules::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function processedTransactions()
    {
        return $this->hasMany(PointTransactions::class, 'processed_by');
    }

    public function processedRedemptions()
    {
        return $this->hasMany(PointRedemptions::class, 'processed_by');
    }
}
