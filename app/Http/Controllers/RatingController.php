<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Product;
use App\Models\Rating;

class RatingController extends Controller
{
    /**
     * Handle product ratings.
     */
    public function store(Request $request, Product $product)
    {
        if ($request->has('remove')) {
            Rating::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->delete();
                
            return back()->with('success', 'Your rating has been removed.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Rating::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ],
            [
                'rating' => $request->rating,
                'trip_id' => null,
            ]
        );

        return back()->with('success', 'Thank you for your rating!');
    }

    /**
     * Handle trip ratings.
     */
    public function storeTrip(Request $request, Trip $trip)
    {
        if ($request->has('remove')) {
            Rating::where('user_id', auth()->id())
                ->where('trip_id', $trip->id)
                ->delete();
                
            return back()->with('success', 'Your rating has been removed.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Rating::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'trip_id' => $trip->id,
            ],
            [
                'rating' => $request->rating,
                'product_id' => null,
            ]
        );

        return back()->with('success', 'Thank you for your trip rating!');
    }
}