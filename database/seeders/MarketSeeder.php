<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Vendor;

class MarketSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Mock Vendors (Shops)
        $supermarket = Vendor::create([
            'name' => 'Santa Lucia Supermarket',
            'type' => 'scraped',
            'location' => 'Bonamoussadi, Douala',
            'phone_number' => '+237600000000'
        ]);

        $quarterStore = Vendor::create([
            'name' => "Boutique d'Amadou",
            'type' => 'local',
            'location' => 'Ngoa-Ekelle, Yaoundé',
            'phone_number' => '+237611111111'
        ]);

        // 2. Create Mock Products
        $product1 = Product::create([
            'name' => 'Riz Mémé Cassé 25kg',
            'brand' => 'Mémé',
            'category' => 'Alimentation',
            'image_url' => 'https://via.placeholder.com/150'
        ]);

        $product2 = Product::create([
            'name' => 'Huile Végétale Mayor 1L',
            'brand' => 'Mayor',
            'category' => 'Alimentation',
            'image_url' => 'https://via.placeholder.com/150'
        ]);

        // 3. Link Products to Vendors with Prices (CFA Francs)
        // Riz Mémé is cheaper at the quarter store, but Mayor Oil is cheaper at the supermarket
        $product1->vendors()->attach([
            $supermarket->id => ['price' => 18500, 'is_available' => true],
            $quarterStore->id => ['price' => 17900, 'is_available' => true]
        ]);

        $product2->vendors()->attach([
            $supermarket->id => ['price' => 1450, 'is_available' => true],
            $quarterStore->id => ['price' => 1600, 'is_available' => true]
        ]);
    }
}