<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Notifications\PriceDropNotification; // Ensure this class exists
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorDashboardController extends Controller
{
    public function show()
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) return redirect()->route('vendor.login.form');

        $catalogProducts = Product::orderBy('name', 'asc')->get();
        return view('vendor.dashboard', compact('vendor', 'catalogProducts'));
    }

    public function updatePrice(Request $request) 
    {
        $request->validate([
            'new_product_name' => 'required|string',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric',
            'category'         => 'required|string',
            'product_image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product = Product::firstOrCreate(['name' => $request->new_product_name]);

        $updateData = [
            'category'    => $request->category,
            'description' => $request->description,
        ];

        if ($request->hasFile('product_image')) {
            $path = $request->file('product_image')->store('products', 'public');
            $updateData['image_url'] = 'storage/' . $path;
        }

        $product->update($updateData);

        $vendor = Auth::guard('vendor')->user();

        // --- PRICE DROP LOGIC ---
        // Get the current price from the pivot table before updating
        $existingProduct = $vendor->products()->where('product_id', $product->id)->first();
        $oldPrice = $existingProduct ? $existingProduct->pivot->price : null;
        $newPrice = $request->price;

        // Sync the new price
        $vendor->products()->syncWithoutDetaching([
            $product->id => ['price' => $newPrice]
        ]);

        // If price decreased, notify users who wishlisted this product
        if ($oldPrice && $newPrice < $oldPrice) {
            $users = User::whereHas('wishlist', function($query) use ($product) {
                $query->where('product_id', $product->id);
            })->get();

            foreach ($users as $user) {
                $user->notify(new PriceDropNotification($product, $oldPrice, $newPrice));
            }
        }
        // --- END PRICE DROP LOGIC ---

        return redirect()->back()->with('success', 'Product registered/updated successfully!');
    }

    public function destroy(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if ($request->has('permanently_delete')) $vendor->delete();

        Auth::guard('vendor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('product.search');
    }
}