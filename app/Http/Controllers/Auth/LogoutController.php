<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Log the user out of the default guard
        Auth::logout();

        // Invalidate the session and regenerate the token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the home page or login page
        return redirect()->route('product.search'); 
    }
}