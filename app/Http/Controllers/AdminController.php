<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Agency;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    
    public function dashboard()
    {
        $totalVendors = Vendor::count();
        $totalAgencies = Agency::count();
        $totalProducts = Product::count();
        $popularProducts = Product::withCount('vendors')->orderBy('vendors_count', 'desc')->take(5)->get();
        
       $totalUsers = User::where('email', '!=', 'joelletchoffo92@gmail.com')->count();
        $pendingVendors = Vendor::where('is_approved', false)->get();
       $pendingAgencies = Agency::where('is_approved', false)
                        ->orWhereNull('is_approved')
                        ->get();
        return view('admin.dashboard', compact(
            'totalVendors', 
            'totalAgencies', 
            'totalProducts', 
            'totalUsers',
            'popularProducts', 
            'pendingVendors', 
            'pendingAgencies'
        ));
    }

    // --- Vendor Methods ---
    public function approveVendor($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->is_approved = true;
        $vendor->save();
        return back()->with('success', 'Vendor approved successfully!');
    }

  public function showVendor($id)
{
    $vendor = Vendor::findOrFail($id);
    return view('admin.vendors.show', compact('vendor'));
}

    public function rejectVendor($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete(); 
        return redirect()->route('admin.dashboard')->with('success', 'Vendor shop rejected and deleted successfully.');
    }

    // --- Agency Methods ---
 public function approveAgency($id)
{
    $agency = Agency::findOrFail($id);
    
    // Explicitly approve the agency
    $agency->is_approved = 1;
    $agency->verified_at = now();
    $agency->save();

    return back()->with('success', 'Agency approved successfully!');
}

    public function showAgency($id)
    {
        $agency = Agency::findOrFail($id);
        return view('admin.agencies.show', compact('agency'));
    }

    public function rejectAgency($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Agency rejected and deleted successfully.');
    }

    // --- Management Views ---
    public function manageAgencies()
    {
        $agencies = Agency::all(); 
        return view('admin.agencies', compact('agencies'));
    }

    public function manageVendors()
    {
        $vendors = Vendor::all(); 
        return view('admin.vendors', compact('vendors'));
    }

    public function manageProducts()
{
    $products = Product::with('vendors')->withCount('vendors')->get();
    return view('admin.products', compact('products'));
}
    public function verifyVendor($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->verified_at = now(); 
        $vendor->save();
        return back(); 
    }
  

// Toggle Vendor Active/Approval Status (Deactivate)
public function toggleVendorStatus($id)
{
    $vendor = Vendor::findOrFail($id);
    // Switches boolean status (true becomes false, false becomes true)
    $vendor->is_approved = !$vendor->is_approved;
    $vendor->save();

    return back()->with('success', 'Vendor status updated successfully.');
}

// Delete Vendor
public function deleteVendor($id)
{
    $vendor = Vendor::findOrFail($id);
    
    // Optional: detach products or delete related records if needed
    $vendor->products()->detach(); 
    
    // Delete the vendor account
    $vendor->delete();

    return back()->with('success', 'Vendor deleted successfully.');
}
// Toggle Agency Active/Approval Status (Deactivate/Activate)
    public function toggleAgencyStatus($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->is_approved = !$agency->is_approved;
        $agency->save();

        return back()->with('success', 'Agency status updated successfully.');
    }

    // Delete Agency
    public function deleteAgency($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->delete();

        return back()->with('success', 'Agency deleted successfully.');
    }
}