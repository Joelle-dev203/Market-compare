<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\TripPriceHistory;
use App\Models\PriceAlert;
use App\Notifications\PriceDropNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TripController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     * We exclude 'show' so it remains public.
     */
    public static function middleware(): array
    {
        return [
           new Middleware('auth:agency', except: ['show']),
        ];
    }

    /**
     * Display the specified trip.
     */
    public function show($id)
    {
        $trip = Trip::with(['agency', 'route'])->findOrFail($id);

        // 1. More from this Agency
        $agencyTrips = Trip::where('agency_id', $trip->agency_id)
            ->where('id', '!=', $trip->id)
            ->limit(3)
            ->get();

        // 2. Similar trips: Search by arrival city, excluding the current agency
        $similarTrips = Trip::whereHas('route', function($query) use ($trip) {
                $query->where('arrival_city', $trip->route->arrival_city);
            })
            ->where('agency_id', '!=', $trip->agency_id)
            ->limit(3)
            ->get();

        return view('trips.show', compact('trip', 'agencyTrips', 'similarTrips'));
    }

    /**
     * Store a newly created trip.
     */
    public function store(Request $request) 
    {
        $request->validate([
            'type' => 'required|in:flight,bus',
            'origin' => 'required|string',
            'destination' => 'required|string',
            'schedules' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $route = \App\Models\Route::firstOrCreate([
                'departure_city' => $request->origin,
                'arrival_city' => $request->destination,
            ]);

            // Format pricing data depending on transport type
            $pricesData = [];
            
            if ($request->type === 'flight') {
                $classNames = $request->input('class_names', []);
                $oneWayPrices = $request->input('oneway_prices', []);
                $roundTripPrices = $request->input('roundtrip_prices', []);
                $seatTypes = $request->input('seat_types', []);

                foreach ($classNames as $index => $className) {
                    $trimmedName = trim($className);
                    if (!empty($trimmedName)) {
                        $pricesData[$trimmedName] = [
                            'one_way' => $oneWayPrices[$index] ?? 0,
                            'round_trip' => $roundTripPrices[$index] ?? null,
                            'seat_feature' => $seatTypes[$index] ?? null,
                        ];
                    }
                }
            } else {
    // Bus classes structure capturing multiple times/prices per class
    $busClasses = $request->input('bus_classes', []);
    foreach ($busClasses as $bClass) {
        $busName = trim($bClass['name'] ?? '');
        if (!empty($busName)) {
            $pricesData[$busName] = [
                'price' => $bClass['price'] ?? 0,
                // Ensure times is stored as an array of hours entered
                'times' => isset($bClass['times']) ? array_filter($bClass['times']) : [],
            ];
        }
    }
}

            $trip = Trip::create([
                'agency_id' => Auth::guard('agency')->id(),
                'route_id' => $route->id, 
                'type' => $request->type,
                'schedules' => $request->input('schedules'), 
                'prices' => $pricesData,
            ]);

            // Save history for tracking price changes across flight classes
            if ($request->type === 'flight') {
                $classNames = $request->input('class_names', []);
                $oneWayPrices = $request->input('oneway_prices', []);

                foreach ($classNames as $index => $className) {
                    $trimmedName = trim($className);
                    if (!empty($trimmedName)) {
                        TripPriceHistory::create([
                            'trip_id'    => $trip->id,
                            'class_name' => $trimmedName,
                            'price'      => $oneWayPrices[$index] ?? 0,
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'Route, schedule and prices registered successfully!');
    }

    /**
     * Update trip prices and dispatch email alerts if any user is tracking this trip.
     */
    public function updatePrices(Request $request, $id)
    {
        $trip = Trip::where('agency_id', Auth::guard('agency')->id())->findOrFail($id);

        $request->validate([
            'class_names' => 'required|array',
            'oneway_prices' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $trip) {
            $classNames = $request->input('class_names', []);
            $oneWayPrices = $request->input('oneway_prices', []);
            $roundTripPrices = $request->input('roundtrip_prices', []);
            $seatTypes = $request->input('seat_types', []);

            $pricesData = [];

            foreach ($classNames as $index => $className) {
                $trimmedName = trim($className);
                if (!empty($trimmedName)) {
                    $pricesData[$trimmedName] = [
                        'one_way' => $oneWayPrices[$index] ?? 0,
                        'round_trip' => $roundTripPrices[$index] ?? null,
                        'seat_feature' => $seatTypes[$index] ?? null,
                    ];

                    // Save history record
                    TripPriceHistory::create([
                        'trip_id'    => $trip->id,
                        'class_name' => $trimmedName,
                        'price'      => $oneWayPrices[$index] ?? 0,
                    ]);
                }
            }

            // Update trip JSON prices array
            $trip->update([
                'prices' => $pricesData,
            ]);
        });

        // Find users tracking this trip and trigger price-drop email notifications
        $alerts = PriceAlert::where('trip_id', $trip->id)
                            ->where('is_sent', false)
                            ->with('user')
                            ->get();

        foreach ($alerts as $alert) {
            if ($alert->user) {
                $alert->user->notify(new PriceDropNotification($trip));
                $alert->update(['is_sent' => true]);
            }
        }

        return back()->with('success', 'Trip prices updated and alerts dispatched successfully!');
    }

    /**
     * Delete the specified trip completely from the database.
     */
    public function destroy($id)
    {
        $trip = Trip::where('agency_id', Auth::guard('agency')->id())->findOrFail($id);

        DB::transaction(function () use ($trip) {
            TripPriceHistory::where('trip_id', $trip->id)->delete();
            PriceAlert::where('trip_id', $trip->id)->delete();
            $trip->delete();
        });

        return back()->with('success', 'Trip deleted successfully!');
    }

    /**
     * Rate the specified trip.
     */
    public function rateTrip(Request $request, \App\Models\Trip $trip)
{
    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
    ]);

    // If the user sends a 'remove' request, delete their rating for this specific trip
    if ($request->has('remove')) {
        \App\Models\Rating::where('user_id', auth()->id())
            ->where('trip_id', $trip->id)
            ->delete();
            
        return back()->with('success', 'Your rating has been removed.');
    }

    // Safely update or create using explicit trip_id and user_id scoping
    \App\Models\Rating::updateOrCreate(
        [
            'user_id' => auth()->id(),
            'trip_id' => $trip->id,
        ],
        [
            'rating' => $request->rating,
            'product_id' => null, // Ensure product_id stays null for trip ratings
        ]
    );

    return back()->with('success', 'Thank you for your trip rating!');
}
public function rate(Request $request, $id)
{
    // Add your rating logic here, for example:
    $trip = Trip::findOrFail($id);
    
    // Save the rating...
    
    return back()->with('success', 'Rating submitted successfully!');
}
}