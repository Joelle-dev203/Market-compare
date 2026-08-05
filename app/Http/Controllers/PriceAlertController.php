<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PriceAlert;
use Illuminate\Support\Facades\Auth;

class PriceAlertController extends Controller
{
    // Existing product price alert toggle
    public function toggle($productId)
    {
        $user = Auth::user();

        // Check if the alert already exists for this user and product
        $alert = PriceAlert::where('user_id', $user->id)
                            ->where('product_id', $productId)
                            ->first();

        if ($alert) {
            // If it exists, remove it (Toggle Off)
            $alert->delete();
            return response()->json(['status' => 'removed']);
        } else {
            // If it doesn't exist, create it (Toggle On)
            PriceAlert::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'is_sent' => false,
            ]);
            return response()->json(['status' => 'added']);
        }
    }

    // New trip price alert toggle
    public function toggleTripAlert($tripId)
    {
        $user = Auth::user();

        // Check if the alert already exists for this user and trip
        $alert = PriceAlert::where('user_id', $user->id)
                            ->where('trip_id', $tripId)
                            ->first();

        if ($alert) {
            // If it exists, remove it (Toggle Off)
            $alert->delete();
            return response()->json(['status' => 'removed']);
        } else {
            // If it doesn't exist, create it (Toggle On)
            PriceAlert::create([
                'user_id' => $user->id,
                'product_id' => null, // Kept null so product logic remains untouched
                'trip_id' => $tripId,
                'is_sent' => false,
            ]);
            return response()->json(['status' => 'added']);
        }
    }
}