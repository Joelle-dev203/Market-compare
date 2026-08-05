<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    // Define the fields that are mass-assignable
    protected $fillable = [
        'name',         
        'image_url',    
        'contact_phone', 
        'agency_id',
        'route_id',
        'type',
        'schedules',    
        'prices',
        'stop_details', 
    ];

    // Ensure JSON columns are treated as arrays automatically
    protected $casts = [
        'schedules' => 'array',
        'prices' => 'array',
    ];

    // Automatically eager-load agency, route, and variants with their classes if needed
    protected $with = ['agency', 'route', 'variants.classes'];

    public function agency() {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function route() {
        return $this->belongsTo(\App\Models\Route::class);
    }

    // NEW: Relationship to handle One-Way and Round-Trip variants dynamically
    public function variants()
    {
        return $this->hasMany(TripVariant::class, 'trip_id');
    }

    public function priceHistory()
    {
        return $this->hasMany(TripPriceHistory::class, 'trip_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'trip_id');
    }
}