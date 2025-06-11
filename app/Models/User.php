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
    protected $fillable = ['name', 'email', 'password', 'phone', 'address', 'district', 'postal_code', 'balance'];

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
    public function recycleBin()
    {
        return $this->hasOne(Bin::class)->where('type', 'recycle');
    }

    /**
     * Get the non-recycle bin for the user.
     */
    public function nonRecycleBin()
    {
        return $this->hasOne(Bin::class)->where('type', 'non_recycle');
    }

    /**
     * Check if user is active.
     */

    /**
     * Get full address.
     */
    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->district .
               ($this->postal_code ? ' ' . $this->postal_code : '');
    }

    /**
     * Get photo URL.
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/users/' . $this->photo) : asset('images/default-avatar.png');
    }
}
