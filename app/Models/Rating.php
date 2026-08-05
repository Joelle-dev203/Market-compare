<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    // Added 'trip_id' to fillable to support the new functionality
    protected $fillable = ['product_id', 'trip_id', 'user_id', 'rating'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}