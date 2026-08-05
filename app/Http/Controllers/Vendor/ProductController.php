<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PriceHistory; // <-- Make sure to import your PriceHistory model at the top
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // Only vendors can access this
    public function __construct()
    {
        $this->middleware('auth:vendor');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'price'    => 'required|numeric',
            'image'    => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'category' => 'required|string',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        // Create the product
        $product = Product::create([
            'name'     => $request->name,
            'price'    => $request->price,
            'image'    => $imagePath,
            'category' => $request->category,
        ]);

        $vendorId = Auth::guard('vendor')->id();

        // Attach to the authenticated vendor with the initial price in the pivot table
        $product->vendors()->attach($vendorId, [
            'price' => $request->price
        ]);

        // Log the initial price into PriceHistory as the starting baseline (e.g., 2000)
        PriceHistory::create([
            'product_id' => $product->id,
            'vendor_id'  => $vendorId,
            'price'      => $request->price,
        ]);

        return redirect()->route('vendor.dashboard')->with('success', 'Product added successfully!');
    }
}