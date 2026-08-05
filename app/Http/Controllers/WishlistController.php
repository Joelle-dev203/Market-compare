<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\PriceAlert;

class WishlistController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return view('auth.register'); 
        }

        // Load both products and trips from the user's wishlist
        $products = auth()->user()->wishlist()->with('vendors')->get();
        $trips = Wishlist::where('user_id', auth()->id())->whereNotNull('trip_id')->with('trip.agency', 'trip.route')->get();

        return view('wishlist', compact('products', 'trips'));
    }

    // Existing product toggle
    public function toggle(Request $request, $productId)
    {
        $user = Auth::user();
        $user->wishlist()->toggle($productId);
        return response()->json(['status' => 'success']);
    }

    // New trip toggle handler for AJAX requests
    public function toggleTrip($tripId)
    {
        $userId = auth()->id();

        $wishlist = Wishlist::where('user_id', $userId)
                            ->where('trip_id', $tripId)
                            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['status' => 'removed', 'message' => 'Trip removed from wishlist']);
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => null, 
                'trip_id' => $tripId,
            ]);
            return response()->json(['status' => 'added', 'message' => 'Trip added to wishlist']);
        }
    }

    // Delete method to remove from wishlist and corresponding price_alert tables
    public function destroy($id)
    {
        $userId = auth()->id();

        $wishlist = Wishlist::where('id', $id)
                            ->where('user_id', $userId)
                            ->firstOrFail();

        // Delete corresponding price alert if it's tied to a product or trip
        if ($wishlist->product_id) {
            PriceAlert::where('user_id', $userId)
                      ->where('product_id', $wishlist->product_id)
                      ->delete();
        } elseif ($wishlist->trip_id) {
            PriceAlert::where('user_id', $userId)
                      ->where('trip_id', $wishlist->trip_id)
                      ->delete();
        }

        // Finally, delete the wishlist entry itself
        $wishlist->delete();

        return redirect()->back()->with('success', 'Item successfully removed from your wishlist and alerts.');
    }
}