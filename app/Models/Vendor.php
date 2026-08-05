<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Vendor extends Authenticatable
{
    use HasFactory;

    protected $table = 'vendors'; 
    protected $fillable = [
        'name', 'email', 'password', 'phone_number', 'location', 'is_approved', 'verified_at',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('price', 'is_available')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}