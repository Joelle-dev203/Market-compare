<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripPriceHistory extends Model
{
    protected $fillable = ['trip_id', 'class_name', 'price'];
}