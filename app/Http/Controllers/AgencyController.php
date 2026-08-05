<?php
namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgencyController extends Controller
{
    // 1. Show Registration
    public function create() { return view('agencies.register'); }

    // 2. Store & Auto-Login
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:agencies,email', // Added email
            'password' => 'required|min:8',
            'logo' => 'required|image|max:2048',
            'type' => 'required|in:flight,bus',
            'contact_phone' => 'required|string',
        ]);

        $logoPath = $request->file('logo')->store('logos', 'public');

        $agency = Agency::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password must be hashed!
            'logo_path' => $logoPath,
            'type' => $request->type,
            'contact_phone' => $request->contact_phone,
        ]);

        // Automatically log them in
        Auth::guard('agency')->login($agency);

        return redirect()->route('agency.dashboard')->with('success', 'Welcome to your portal!');
    }
}