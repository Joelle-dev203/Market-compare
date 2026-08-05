<?php
namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TripPriceHistory;

class DashboardController extends Controller
{
    public function index()
{
    $agency = Auth::guard('agency')->user();
    
    // Eager load the 'route' relationship to avoid N+1 queries
    $trips = \App\Models\Trip::where('agency_id', $agency->id)
                ->with('route') 
                ->get();

    return view('agency.dashboard', compact('trips'));
}
public function store(Request $request) {
    // Validate request
    $request->validate([
        'type' => 'required',
        'origin' => 'required',
        'destination' => 'required',
        'class_names' => 'required|array',
        'prices' => 'required|array',
    ]);

    // Save to database
    \App\Models\Trip::create([
        'agency_id' => Auth::guard('agency')->id(),
        'type' => $request->type,
        'origin' => $request->origin,
        'destination' => $request->destination,
        'prices' => json_encode(array_combine($request->class_names, $request->prices)),
    ]);

    return back()->with('success', 'Route registered!');
}
// Update a specific price class
public function updatePrice(Request $request, $id) {
    $trip = \App\Models\Trip::findOrFail($id);
    $prices = json_decode($trip->prices, true);
    
    // Update the specific class price
    $prices[$request->class_name] = $request->price;
    
    $trip->update(['prices' => json_encode($prices)]);
    return back()->with('success', 'Price updated!');
}

// Fetch history (requires a separate 'price_histories' table)
public function showHistory($id)
{
    $trip = \App\Models\Trip::with('route')->findOrFail($id);

    // Fetch from the new specific table
    $history = TripPriceHistory::where('trip_id', $id)
                                ->orderBy('created_at', 'desc')
                                ->get();
    
    return view('agency.history', compact('trip', 'history'));
}
}