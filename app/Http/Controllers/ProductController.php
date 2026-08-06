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
    $productsCount = Product::count();
    $vendorCount = Vendor::count();

    // 1. Fetch products with relationships
    $query = Product::query()
        ->with(['vendors' => function($q) {
            $q->with(['products' => function($sq) {
                $sq->with('priceHistories');
            }]);
        }])
        ->withCount('ratings')
        ->withAvg('ratings', 'rating');

    // 2. Apply filters if active
    if ($request->filled('query')) {
        $query->where('name', 'like', '%' . $request->query('query') . '%');
    }

    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    // 3. If no filter, get all to shuffle randomly via PHP collection
    if (!$request->filled('query') && !$request->filled('category')) {
        $allProducts = $query->get()->shuffle(); // This guarantees a true shuffle every single refresh!
        
        // Manually paginate the shuffled collection
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 12;
        $currentItems = $allProducts->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems, 
            $allProducts->count(), 
            $perPage, 
            $currentPage, 
            ['path' => $request->url(), 'query' => $request->query()]
        );
    } else {
        $products = $query->latest()->paginate(12);
    }

    return view('search', [
        'products' => $products,
        'productsCount' => $productsCount,
        'vendorCount' => $vendorCount,
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