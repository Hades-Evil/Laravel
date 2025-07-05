<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function isAdmin(): bool
    {
        return $this->utype === 'ADM';
    }

    public function isUser(): bool
    {
        return $this->utype === 'USR';
    }

    /**
     * Check if this is the main admin seeder account
     * @return bool
     */
    public function isMainAdmin(): bool
    {
        return $this->isAdmin() && $this->email === 'admin2@gmail.com';
    }

    /**
     * Check if this is a shop owner (registered admin, not seeder admin)
     * @return bool
     */
    public function isShopOwner(): bool
    {
        return $this->isAdmin() && $this->email !== 'admin2@gmail.com';
    }
    
    /**
     * Get all products owned by this user
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }
}
