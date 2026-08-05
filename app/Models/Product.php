<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'brand', 'price', 'category', 'description', 'image_url'];

    /**
     * Get all vendors selling this product with their respective prices.
     */
    public function vendors() 
    {
        return $this->belongsToMany(Vendor::class, 'product_vendor')
                    ->withPivot('price', 'updated_at');
    }

    /**
     * Get all price history records for this product.
     */
    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class);
    }

    /**
     * Get the wishlist entries associated with this product.
     */
    public function wishlists() 
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    /**
     * Get the price alerts associated with this product.
     */
    public function priceAlerts()
    {
        return $this->hasMany(PriceAlert::class, 'product_id');
    }

    /**
     * Get all ratings for this product.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Helper to get the average rating.
     */
    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    /**
     * Accessor to get the cheapest vendor for this product.
     */
    public function getCheapestVendorAttribute() 
    {
        return $this->vendors()->orderBy('price', 'asc')->first();
    }
}