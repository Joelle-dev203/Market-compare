<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    // Tell Laravel which fields can be filled
    protected $fillable = [
        'product_id',
        'vendor_id',
        'price',
    ];
}