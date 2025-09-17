<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the customer dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please complete your customer profile first.');
        }

        // Get customer's booking statistics
        $totalBookings = Booking::where('customer_id', $customer->id)->count();

        $upcomingBookings = Booking::where('customer_id', $customer->id)
            ->where('check_in_date', '>=', now())
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        $pastBookings = Booking::where('customer_id', $customer->id)
            ->where('check_out_date', '<', now())
            ->whereIn('status', ['checked_out', 'cancelled'])
            ->count();

        // Get recent bookings
        $recentBookings = Booking::with(['room.roomCategory'])
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('customer.dashboard', compact(
            'totalBookings',
            'upcomingBookings',
            'pastBookings',
            'recentBookings'
        ));
    }
}
