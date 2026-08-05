<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Trip;

class ProductSearchController extends Controller
{
    public function search(Request $request)
    {
        $rawQuery = trim($request->input('query'));
        $categoryFilter = $request->input('category');
        $locationFilter = $request->input('location');

        $travelCategories = ['Flights', 'Buses', 'Car Rental'];
        
        // Clean up common phrasing like "search for flight", "find bus"
        $cleanedQuery = str_ireplace(['search for', 'search', 'find', 'book', 'show me'], '', $rawQuery);
        $searchTerm = trim($cleanedQuery);

        $lowerSearch = strtolower($searchTerm);
        $isGeneralTripSearch = in_array($lowerSearch, ['trip', 'trips', 'travel']);

        // Only trigger travel filters if the search term explicitly and cleanly matches travel keywords
        if ($lowerSearch === 'flight' || $lowerSearch === 'flights' || str_contains($lowerSearch, 'flight ')) {
            $categoryFilter = 'Flights';
            $searchTerm = null;
        } elseif ($lowerSearch === 'bus' || $lowerSearch === 'buses' || str_contains($lowerSearch, 'bus ')) {
            $categoryFilter = 'Buses';
            $searchTerm = null;
        } elseif ($lowerSearch === 'car rental' || str_contains($lowerSearch, 'car rental')) {
            $categoryFilter = 'Car Rental';
            $searchTerm = null;
        } elseif ($isGeneralTripSearch) {
            $searchTerm = null; 
        }

        // 1. Products Query (Only for physical products, excluding travel categories)
        $productsQuery = Product::with(['vendors' => function ($query) use ($locationFilter) {
            $query->where('is_approved', true);
            if (!empty($locationFilter)) {
                $query->where('location', $locationFilter);
            }
        }])->whereHas('vendors', function($query) use ($locationFilter) {
            $query->where('is_approved', true);
            if (!empty($locationFilter)) {
                $query->where('location', $locationFilter);
            }
        });

        if (!empty($searchTerm)) {
            $productsQuery->where(function($subQuery) use ($searchTerm) {
                $subQuery->where('name', 'LIKE', '%' . $searchTerm . '%')
                         ->orWhere('brand', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Only apply category filter to products if it is NOT a travel category
        if (!empty($categoryFilter) && !in_array($categoryFilter, $travelCategories)) {
            $productsQuery->where('category', $categoryFilter);
        }

        // Hide physical products completely if a travel category or general trip search is active
        if ((!empty($categoryFilter) && in_array($categoryFilter, $travelCategories)) || $isGeneralTripSearch) {
            $productsQuery->whereRaw('1 = 0');
        }

        $products = $productsQuery->paginate(12)->withQueryString();

        // 2. Trips Query (For Flights, Buses, Car Rental)
        $tripsQuery = Trip::with(['route', 'agency']);
        
        // If a standard product category (like computers or phones) is selected, force trips to return nothing!
        if (!empty($categoryFilter) && !in_array($categoryFilter, $travelCategories) && !$isGeneralTripSearch) {
            $tripsQuery->whereRaw('1 = 0');
        } else {
            if (!empty($searchTerm)) {
                $tripsQuery->whereHas('route', function($q) use ($searchTerm) {
                    $q->where('departure_city', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('arrival_city', 'LIKE', '%' . $searchTerm . '%');
                });
            }
            
            if (!empty($locationFilter)) {
                $tripsQuery->whereHas('route', function($q) use ($locationFilter) {
                    $q->where('departure_city', $locationFilter);
                });
            }

            // Filter trips if a specific travel category is active
            if (!empty($categoryFilter) && in_array($categoryFilter, $travelCategories)) {
                $dbType = match($categoryFilter) {
                    'Flights' => 'flight',
                    'Buses' => 'bus',
                    'Car Rental' => 'car rental',
                    default => strtolower($categoryFilter)
                };
                
                $tripsQuery->where(function($q) use ($dbType) {
                    $q->where('type', $dbType)
                      ->orWhereHas('agency', function($agQuery) use ($dbType) {
                          $agQuery->where('type', $dbType);
                      });
                });
            }
        }

        $trips = $tripsQuery->paginate(6)->withQueryString();

        // 3. Locations
        $availableLocations = Vendor::where('is_approved', true)
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->pluck('location');

        return view('search', compact(
            'products', 'trips', 'searchTerm', 'categoryFilter', 'locationFilter', 'availableLocations'
        ));
    }
}