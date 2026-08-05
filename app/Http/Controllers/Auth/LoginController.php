<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Agency;
use App\Models\Vendor;
use App\Models\User;

class LoginController extends Controller
{
    // Now returns a generic login view or you can create separate ones
    public function showLoginForm()
    {
        return view('vendor.login'); 
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // 1. ADMIN CHECK (Must come first!)
    if ($request->email === 'joelletchoffo92@gmail.com') {
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid admin credentials.']);
    }

    // 2. AGENCY LOGIN
    if (Auth::guard('agency')->attempt($credentials)) {
        if (Auth::guard('agency')->user()->is_approved) {
            $request->session()->regenerate();
            return redirect()->route('agency.dashboard');
        } else {
            Auth::guard('agency')->logout();
            return back()->withErrors(['email' => 'Agency account pending approval.']);
        }
    }

    // 3. VENDOR LOGIN
    if (Auth::guard('vendor')->attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('vendor.dashboard');
    }

    // 4. STANDARD USER LOGIN
    if (Auth::guard('web')->attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('product.search'); // Change '/' to your desired path
    }

    return back()->withInput($request->only('email'))
                 ->withErrors(['email' => 'Invalid credentials.']);
}
}