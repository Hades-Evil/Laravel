<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VendorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $vendorRequest = $user->vendorRequests()->latest()->first();
        
        return view('user.index', compact('vendorRequest'));
    }

    public function requestVendor()
    {
        return view('user.vendor-request');
    }

    public function storeVendorRequest(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_description' => 'required|string|max:1000',
            'business_type' => 'required|in:vendor',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check if user already has a pending request
        if ($user->hasVendorRequest()) {
            return redirect()->back()->with('error', 'You already have a pending vendor request.');
        }

        // Check if user already has an approved request
        if ($user->hasApprovedVendorRequest()) {
            return redirect()->back()->with('error', 'You are already an approved vendor.');
        }

        VendorRequest::create([
            'user_id' => $user->id,
            'business_name' => $request->business_name,
            'business_description' => $request->business_description,
            'business_type' => $request->business_type,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => 'pending',
        ]);

        return redirect()->route('user.index')->with('success', 'Your vendor request has been submitted successfully! An admin will review it soon.');
    }
}
