<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\PriceHistory;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        // Your existing search logic...
    $searchTerm = $request->input('search');
    $locationFilter = $request->input('location');
    
    // Add these two lines to fetch the live counts
    $productsCount = Product::count();
    $vendorCount = Vendor::count(); // Or Store::count() depending on your model name

    // Pass them into the view compact array
    return view('search', compact('productsCount', 'vendorCount', 'searchTerm', 'locationFilter' /* plus any other variables you already pass */));
        // 1. Load products with vendors, ensuring we can access pivot data
        $query = Product::query()
            ->with(['vendors' => function($q) {
                // Eager load the latest history for each vendor to display price changes
                $q->with(['products' => function($sq) {
                    $sq->with('priceHistories');
                }]);
            }])
            ->withCount('ratings')
            ->withAvg('ratings', 'rating');

        if ($request->filled('query')) {
            $query->where('name', 'like', '%' . $request->query . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if (!$request->filled('query') && !$request->filled('category')) {
            $query->inRandomOrder();
        } else {
            $query->latest();
        }

        $products = $query->paginate(12);

        return view('product.search', [
            'products' => $products,
            'searchTerm' => $request->query('query'),
            'selectedCategory' => $request->category,
        ]);
    }

    public function show($id)
    {
        // Load product with its vendors and their specific price history
        $product = Product::with(['vendors' => function($q) {
            $q->with(['products' => function($sq) {
                $sq->with(['priceHistories' => function($h) {
                    $h->latest(); // Get most recent history
                }]);
            }]);
        }])
        ->withCount('ratings')
        ->withAvg('ratings', 'rating')
        ->findOrFail($id);
            
        $userRating = auth()->check() 
            ? $product->ratings()->where('user_id', auth()->id())->value('rating') 
            : 0;

        // Fetch other products from the same vendor (excluding current product)
        // Gets the first vendor associated with the product to find more from them
        $vendorId = $product->vendors->pluck('id')->first();
        $vendorProducts = $vendorId 
            ? Product::whereHas('vendors', function($q) use ($vendorId) {
                $q->where('vendors.id', $vendorId);
            })->where('id', '!=', $product->id)->take(4)->get()
            : collect();

        // Fetch similar products (same category, excluding current product)
        $similarProducts = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();
        
        return view('products.show', compact('product', 'userRating', 'vendorProducts', 'similarProducts'));
    }

    public function index() 
    {
        $featuredProducts = Product::inRandomOrder()
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->take(8)
            ->get();
        
        return view('home', compact('featuredProducts'));
    }
}