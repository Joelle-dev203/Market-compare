<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripClass extends Model
{
    protected $fillable = [
        'trip_variant_id',
        'class_name',
        'price',
        'seat_feature',
    ];

    public function variant()
    {
        return $this->belongsTo(TripVariant::class, 'trip_variant_id');
    }
}