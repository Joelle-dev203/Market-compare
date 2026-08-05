<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceAlert extends Model
{
    use HasFactory;

    // Add this to match your database table name explicitly
    protected $table = 'price_alerts'; 

    protected $fillable = [
        'user_id', 
        'product_id', 
        'trip_id', 
        'route_id', 
        'is_sent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}