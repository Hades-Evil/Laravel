<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'utype',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function vendorRequests(): HasMany
    {
        return $this->hasMany(VendorRequest::class);
    }

    public function isAdmin(): bool
    {
        return $this->utype === 'ADM';
    }

    public function isUser(): bool
    {
        return $this->utype === 'USR';
    }

    public function hasVendorRequest(): bool
    {
        return $this->vendorRequests()->where('status', 'pending')->exists();
    }

    public function hasApprovedVendorRequest(): bool
    {
        return $this->vendorRequests()->where('status', 'approved')->exists();
    }
}
