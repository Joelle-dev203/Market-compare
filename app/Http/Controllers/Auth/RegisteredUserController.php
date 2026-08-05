<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function create() 
    {
        // Ensure this view file exists at resources/views/auth/register.blade.php
        return view('auth.register');
    }

    public function store(Request $request) 
    {
        // 1. Validate the input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // 2. Create and Save to the database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Fire the registered event and log the user in
        event(new Registered($user));
        Auth::login($user);

        // 4. Redirect to the search page
        return redirect()->route('product.search')->with('success', 'Registration successful!');
    }
}