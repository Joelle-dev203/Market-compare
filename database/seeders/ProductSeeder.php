<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Vendor;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default fallback base vendor if one doesn't exist yet
        $vendor = Vendor::firstOrCreate(
            ['name' => 'General Market'],
            [
                'type' => 'local',
                'location' => 'Douala',
                'email' => 'market@prixcameroon.com',
                'password' => bcrypt('secret123')
            ]
        );

        // Insert using ONLY the fundamental, un-crashable columns
        $product = Product::firstOrCreate(
            ['name' => 'Huile Mayor 1L'],
            [
                'brand' => 'Mayor',
                'category' => 'Groceries',
                'image_url' => 'https://via.placeholder.com/250'
            ]
        );

        // Sync the price configuration to your relational pivot table
        $product->vendors()->syncWithoutDetaching([
            $vendor->id => ['price' => 1500]
        ]);
    }
}