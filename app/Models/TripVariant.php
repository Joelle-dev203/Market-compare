<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripVariant extends Model
{
    protected $fillable = [
        'trip_id',
        'trip_type',
        'departure_slot',
        'arrival_slot',
        'stop_information',
    ];

    // Automatically eager-load the classes/prices for this variant
    protected $with = ['classes'];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function classes()
    {
        return $this->hasMany(TripClass::class, 'trip_variant_id');
    }
}