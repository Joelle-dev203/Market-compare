<?php

namespace App\Models;

// Remove 'use Illuminate\Database\Eloquent\Model;' if it's there
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Agency extends Authenticatable 
{
    use Notifiable;

    public function trips() {
        return $this->hasMany(Trip::class);
    }

    protected $fillable = [
        'name',
        'location',
        'phone_number',
        'email',
        'password',
        'type',
        'logo_path',
        'is_approved',
        'verified_at',
    ];

    // Optional: Protect password field
    protected $hidden = [
        'password',
    ];
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'trip_id', 'id');
    }
}