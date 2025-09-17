<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the agent dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Get agent's booking statistics
        $totalBookings = Booking::where('agent_id', $user->id)->count();
        $confirmedBookings = Booking::where('agent_id', $user->id)
            ->where('status', 'confirmed')
            ->count();
        $pendingBookings = Booking::where('agent_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Get recent bookings
        $recentBookings = Booking::with(['customer.user', 'room.roomCategory'])
            ->where('agent_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('agent.dashboard', compact(
            'totalBookings',
            'confirmedBookings',
            'pendingBookings',
            'recentBookings'
        ));
    }
}
