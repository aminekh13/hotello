<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the staff dashboard.
     */
    public function index()
    {
        $today = Carbon::today();

        // Get today's activities
        $todayCheckIns = Booking::whereDate('check_in_date', $today)
            ->where('status', 'confirmed')
            ->count();

        $todayCheckOuts = Booking::whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->count();

        // Get recent bookings
        $recentBookings = Booking::with(['customer.user', 'room.roomCategory'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('staff.dashboard', compact(
            'todayCheckIns',
            'todayCheckOuts',
            'recentBookings'
        ));
    }
}
