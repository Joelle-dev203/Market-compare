<?php
namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;

class AdminAgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::all();
        return view('admin.agencies.index', compact('agencies'));
    }

    public function destroy(Agency $agency)
    {
        $agency->delete();
        return redirect()->back()->with('success', 'Agency removed successfully.');
    }
}