<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VendorRegisterController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'type'         => 'required|in:retail,flight,bus',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:vendors,email|unique:agencies,email',
            'password'     => 'required|min:8',
            'phone_number' => 'required|string',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Upload Logo Safely (Handles optional uploads)
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        // 3. Store in the correct table
        if ($request->type === 'retail') {
            Vendor::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'logo'         => $logoPath,
            ]);
        } else {
            Agency::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'type'         => $request->type,
                'logo'         => $logoPath,
            ]);
        }

        return redirect()->route('vendor.login.form')->with('success', 'Registration successful!');
    }
}