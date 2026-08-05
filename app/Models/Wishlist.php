<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    // Define the table name as it appears in your database
    protected $table = 'wishlist';

    // Allow mass assignment for these fields (including new trip/route parameters)
    protected $fillable = [
        'user_id',
        'product_id',
        'trip_id',
        'route_id'
    ];

    /**
     * Get the product associated with this wishlist entry.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the trip associated with this wishlist entry.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    /**
     * Get the user that owns the wishlist item.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}