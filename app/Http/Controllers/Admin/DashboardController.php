<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics for the dashboard
        $totalRooms = Room::count();
        $availableRooms = Room::where('is_available', true)->count();
        $totalBookings = Booking::count();
        $todayCheckIns = Booking::where('check_in_date', today())->where('status', 'confirmed')->count();
        $todayCheckOuts = Booking::where('check_out_date', today())->where('status', 'checked_in')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $totalCustomers = Customer::count();
        $totalAgents = User::where('role', 'agent')->count();

        // Get recent bookings
        $recentBookings = Booking::with(['customer.user', 'room', 'agent'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get occupancy rate for the current month
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $monthlyBookings = Booking::whereBetween('check_in_date', [$currentMonth, $endOfMonth])
            ->where('status', '!=', 'cancelled')
            ->get();

        $totalRoomNights = $monthlyBookings->sum(function ($booking) {
            return $booking->check_in_date->diffInDays($booking->check_out_date);
        });

        $totalPossibleRoomNights = $totalRooms * $currentMonth->diffInDays($endOfMonth);
        $occupancyRate = $totalPossibleRoomNights > 0 ? ($totalRoomNights / $totalPossibleRoomNights) * 100 : 0;

        // Get booking trends for the last 7 days
        $bookingTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $bookingTrends[] = [
                'date' => $date->format('M d'),
                'bookings' => Booking::whereDate('created_at', $date)->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalRooms',
            'availableRooms',
            'totalBookings',
            'todayCheckIns',
            'todayCheckOuts',
            'pendingBookings',
            'totalCustomers',
            'totalAgents',
            'recentBookings',
            'occupancyRate',
            'bookingTrends'
        ));
    }
}
