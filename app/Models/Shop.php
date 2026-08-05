<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'city', 'quarter', 'landmark', 'shop_type'];

    /**
     * The products that belong to the shop.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'prices')
                    ->withPivot('price_xaf', 'source', 'external_url')
                    ->withTimestamps();
    }
}