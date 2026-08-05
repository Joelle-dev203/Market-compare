<?php

namespace App\Http\Controllers;

use App\Models\PriceHistory;
use App\Models\Product; 
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Services\PriceAlertService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewVendorRegistered;

class VendorController extends Controller
{
    public function showRegisterForm() { return view('vendor.register'); }
    
    public function showLoginForm() 
    { 
        return view('vendor.login'); 
    }

 public function register(Request $request)
{
    // 1. Add validation for the logo
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'phone_number' => 'required|string',
        'email' => 'required|email',
        'password' => 'required|string|min:6',
        'type' => 'required|in:retail,flight,bus',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $validated['password'] = Hash::make($request->password);

    // 2. Handle File Upload
    $logoPath = null;
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('products', 'public');
    }

    // Find the admin user (assuming admin email is joelletchoffo92@gmail.com)
    $admin = \App\Models\User::where('email', 'joelletchoffo92@gmail.com')->first();

    // 3. Conditional Logic: Route by Type
    if ($request->type === 'retail') {
        $validated['is_approved'] = false;
        $vendor = \App\Models\Vendor::create($validated);
        
        // ==> FIX: Send notification to admin for Retail Vendor <==
        if ($admin) {
            $admin->notify(new NewVendorRegistered($vendor));
        }
        
        return redirect()->route('vendor.login.form')->with('success', 'Registration successful! Please wait for admin approval.');
    } 
    else {
        // Save to AGENCIES table
        $agency = \App\Models\Agency::create([
            'name' => $validated['name'],
            'location' => $validated['location'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'type' => $request->type,
            'logo_path' => $logoPath,
        ]);

        // ==> FIX: Send notification to admin for Agency <==
        if ($admin) {
            $admin->notify(new NewVendorRegistered($agency));
        }

        return redirect()->route('login')->with('success', 'Agency registration successful! Please wait for admin approval.');
    }
}

 public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // 1. Check for Admin (using the 'web' guard)
    if ($request->email === 'joelletchoffo92@gmail.com') {
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard'); // SUCCESS: Exit here
        }
        return back()->withErrors(['email' => 'Invalid admin credentials.']); // FAIL: Exit here
    }

    // 2. Check for Vendor (using the 'vendor' guard)
    if (Auth::guard('vendor')->attempt($credentials)) {
        $vendor = Auth::guard('vendor')->user();
        if ($vendor->is_approved) {
            $request->session()->regenerate();
            return redirect()->route('vendor.dashboard'); // SUCCESS: Exit here
        }
        
        // Account not approved
        Auth::guard('vendor')->logout();
        return back()->withErrors(['email' => 'Account pending approval.']);
    }

    // 3. Check for Agency (using the 'agency' guard)
    if (Auth::guard('agency')->attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('agency.dashboard'); // SUCCESS: Exit here
    }

    // If none match
    return back()->withErrors(['email' => 'Invalid credentials.']);
}

   public function update_price(Request $request) 
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
        
        // Check if the pivot already exists to track previous pricing
        $existingPivot = $vendor->products()->where('product_id', $product->id)->first();
        $oldPivotPrice = $existingPivot ? $existingPivot->pivot->price : null;

        $vendor->products()->syncWithoutDetaching([
            $product->id => ['price' => $request->price]
        ]);

        // Log to PriceHistory so the initial price serves as a baseline for updates and strikes
        if (!$oldPivotPrice || $oldPivotPrice != $request->price) {
            PriceHistory::create([
                'product_id' => $product->id,
                'vendor_id'  => $vendor->id,
                'price'      => $request->price,
            ]);
        }

        // ==========================================
        // TRIGGER EMAIL ALERT IF PRICE DROPPED
        // ==========================================
        if ($oldPivotPrice && $request->price < $oldPivotPrice) {
            $wishlists = $product->wishlists()->with('user')->get();
            
            foreach ($wishlists as $wishlist) {
                if ($wishlist->user) {
                    $wishlist->user->notify(new \App\Notifications\PriceDropNotification($product, $oldPivotPrice, $request->price));
                }
            }
        }

        return redirect()->back()->with('success', 'Product registered/updated successfully!');
    }

    public function removeProduct($id)
{
    $vendor = Auth::guard('vendor')->user();

    // 1. Delete associated price history records for this product
    PriceHistory::where('product_id', $id)
                ->where('vendor_id', $vendor->id)
                ->delete();

    // 2. Find the product and delete it entirely from the main 'products' table
    $product = $vendor->products()->find($id);
    
    if ($product) {
        // Detach pivot relationship first if needed, then delete the product
        $vendor->products()->detach($id);
        $product->delete();
    }

    return redirect()->back()->with('success', 'Product deleted successfully.');
}
    public function viewPriceHistory($productId)
{
    $vendor = Auth::guard('vendor')->user();
    $product = $vendor->products()->findOrFail($productId);
    
    $histories = PriceHistory::where('product_id', $productId)
                                ->where('vendor_id', $vendor->id)
                                ->orderBy('created_at', 'asc') // Changed from desc to asc
                                ->get();

    return view('vendor.price_history', compact('product', 'histories', 'vendor'));
}

    public function editPrice($id)
    {
        $vendor = Auth::guard('vendor')->user();
        // Load the product and attach the pivot data for the logged-in vendor
        $product = $vendor->products()->findOrFail($id);
        
        return view('vendor.edit_price', compact('product'));
    }

 public function updateExistingPrice(Request $request, $id)
{
    $request->validate([
        'price' => 'required|numeric|min:0',
    ]);

    $vendor = Auth::guard('vendor')->user();
    
    $pivotRecord = \DB::table('product_vendor')
        ->where('vendor_id', $vendor->id)
        ->where('product_id', $id)
        ->first();

    if (!$pivotRecord) {
        return redirect()->back()->with('error', 'Product record not found.');
    }

    $currentPrice = $pivotRecord->price;

    if ($currentPrice != $request->price) {
        // 1. Update the pivot table (handles the product card strike-through instantly)
        \DB::table('product_vendor')
            ->where('vendor_id', $vendor->id)
            ->where('product_id', $id)
            ->update([
                'old_price'  => $currentPrice,
                'price'      => $request->price,
                'updated_at' => now(),
            ]);

        // 2. Also save to your PriceHistory table (so your history log page populates!)
        \App\Models\PriceHistory::create([
            'product_id' => $id,
            'vendor_id'  => $vendor->id,
            'price'      => $request->price,
        ]);
    }

    \App\Services\PriceAlertService::checkAlerts($id, $request->price);

    return redirect()->route('vendor.dashboard')
                     ->with('success', 'Price updated successfully!');
}
public function destroy(Request $request)
    {
        // Get the currently authenticated vendor
        $vendor = Auth::guard('vendor')->user();

        if ($vendor) {
            // 1. Delete all associated products first to avoid foreign key errors
            $vendor->products()->delete();

            // 2. Delete the vendor record
            $vendor->delete();

            // 3. Logout
            Auth::guard('vendor')->logout();
            
            // 4. Invalidate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('product.search')->with('success', 'Your shop has been permanently deleted.');
    }
}