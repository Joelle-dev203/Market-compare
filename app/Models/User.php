<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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

    // Represents the 'wishlist' table (which contains user_id and product_id)
   

// Inside your User class:
public function wishlist(): BelongsToMany
{
    // Assuming your pivot table linking users and products is named 'wishlist' or 'product_user'
    // Adjust 'products' and 'wishlist' to match your actual database table names if needed
    return $this->belongsToMany(Product::class, 'wishlist', 'user_id', 'product_id');
}

    // Represents the 'price_alerts' table
    public function priceAlerts()
    {
        return $this->hasMany(PriceAlert::class, 'user_id');
    }
}