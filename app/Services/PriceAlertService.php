<?php

namespace App\Services;

use App\Models\PriceAlert;
use App\Models\PriceHistory;
use App\Models\Product;
use App\Models\User;
use App\Notifications\PriceDropNotification;
use Illuminate\Support\Facades\Notification;

class PriceAlertService
{
    public static function checkAlerts($productId, $newPrice)
    {
        // 1. Fetch the previous price from PriceHistory (excluding the one that was just created)
        $previousHistory = PriceHistory::where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->skip(1) // Keep this only if you want to compare against the previous history entry, but if there's only 1 record, we need a baseline...
            ->first();

        // Better approach: Get the second newest record, OR if it's the very first update, 
        // fallback to the product's original price or the vendor pivot price before this update.
        $product = Product::find($productId);
        
        // Let's get the record right before the latest one, or fallback to the product's initial state
        $allHistories = PriceHistory::where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        // If we have at least 2 records, index 1 is the previous price. 
        // If we only have 1 record (the first update), what was the price before? 
        // You can use an original price column or treat the first update as a drop if compared to an initial state.
        if ($allHistories->count() > 1) {
            $oldPrice = $allHistories[1]->price;
        } else {
            // This is the VERY FIRST price update. 
            // If you want it to trigger on the first update, define what it compares against:
            // For example, you can fallback to the current $newPrice (no drop) or set a baseline.
            // To force it to treat the first update as a drop/change, you can compare against a default baseline or allow it.
            $oldPrice = $product->original_price ?? $newPrice; 
        }

        // If it's the absolute first record and you want it to trigger an alert immediately:
        if ($allHistories->count() == 1) {
            $oldPrice = $newPrice + 1; // Forces $newPrice < $oldPrice so it triggers on the first entry!
        }

        // Only proceed if the new price is actually lower than the old price
        if ($newPrice >= $oldPrice) {
            \Log::info("Price did not drop. Old: $oldPrice, New: $newPrice");
            return;
        }

        // 2. Fetch all wishlist alerts for this product
        $alerts = PriceAlert::where('product_id', $productId)->get();

        \Log::info("Found " . $alerts->count() . " wishlist alerts for product " . $productId);

        foreach ($alerts as $alert) {
            $user = User::find($alert->user_id);

            if ($user && $user->email) {
                \Log::info("Attempting to send brand new price drop notification to: " . $user->email);
                
                // Send the email notification
                Notification::route('mail', $user->email)
                            ->notify(new PriceDropNotification($product, $oldPrice, $newPrice));
                
                // Reset is_sent to false so any future price drops will trigger a brand new email
                $alert->update(['is_sent' => false]);
                
                \Log::info("Price drop notification sent and reset for: " . $user->email);
            }
        }
    }
}