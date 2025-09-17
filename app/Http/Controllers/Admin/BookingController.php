<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['customer.user', 'room.roomCategory', 'agent']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->filled('date_from')) {
            $query->where('check_in_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('check_out_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('customer.user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $agents = User::where('role', 'agent')->get();
        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];

        return view('admin.bookings.index', compact('bookings', 'agents', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rooms = Room::with('roomCategory')->active()->available()->get();
        $customers = Customer::with('user')->active()->get();
        $agents = User::where('role', 'agent')->get();

        return view('admin.bookings.create', compact('rooms', 'customers', 'agents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'room_id' => 'required|exists:rooms,id',
            'agent_id' => 'nullable|exists:users,id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,confirmed',
        ]);

        $room = Room::findOrFail($request->room_id);
        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);

        // Check room availability
        if (!$room->isAvailableForDates($checkInDate, $checkOutDate)) {
            return back()->withErrors(['room_id' => 'Selected room is not available for the chosen dates.'])
                        ->withInput();
        }

        // Check guest capacity
        if ($room->roomCategory->max_occupancy < $request->guests) {
            return back()->withErrors(['guests' => 'Number of guests exceeds room capacity.'])
                        ->withInput();
        }

        // Calculate total amount
        $nights = $checkInDate->diffInDays($checkOutDate);
        $totalAmount = $room->price_per_night * $nights;

        // Create booking
        $booking = Booking::create([
            'customer_id' => $request->customer_id,
            'room_id' => $request->room_id,
            'agent_id' => $request->agent_id,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'guests' => $request->guests,
            'total_amount' => $totalAmount,
            'special_requests' => $request->special_requests,
            'status' => $request->status,
            'payment_status' => 'pending',
        ]);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking created successfully! Reference: ' . $booking->booking_reference);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $booking->load(['customer.user', 'room.roomCategory', 'agent']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $booking->load(['customer.user', 'room.roomCategory', 'agent']);
        $rooms = Room::with('roomCategory')->active()->get();
        $customers = Customer::with('user')->active()->get();
        $agents = User::where('role', 'agent')->get();

        return view('admin.bookings.edit', compact('booking', 'rooms', 'customers', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'room_id' => 'required|exists:rooms,id',
            'agent_id' => 'nullable|exists:users,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'payment_status' => 'required|in:pending,partial,paid,refunded',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $room = Room::findOrFail($request->room_id);
        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);

        // Check room availability (excluding current booking)
        if (!$room->isAvailableForDates($checkInDate, $checkOutDate, $booking->id)) {
            return back()->withErrors(['room_id' => 'Selected room is not available for the chosen dates.'])
                        ->withInput();
        }

        // Check guest capacity
        if ($room->roomCategory->max_occupancy < $request->guests) {
            return back()->withErrors(['guests' => 'Number of guests exceeds room capacity.'])
                        ->withInput();
        }

        // Calculate total amount
        $nights = $checkInDate->diffInDays($checkOutDate);
        $totalAmount = $room->price_per_night * $nights;

        $booking->update([
            'customer_id' => $request->customer_id,
            'room_id' => $request->room_id,
            'agent_id' => $request->agent_id,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'guests' => $request->guests,
            'total_amount' => $totalAmount,
            'special_requests' => $request->special_requests,
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'paid_amount' => $request->paid_amount,
        ]);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        if (in_array($booking->status, ['checked_in', 'checked_out'])) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Cannot delete completed bookings.');
        }

        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    /**
     * Confirm a booking
     */
    public function confirm(Booking $booking)
    {
        if ($booking->confirm()) {
            return back()->with('success', 'Booking confirmed successfully.');
        }

        return back()->with('error', 'Unable to confirm this booking.');
    }

    /**
     * Check in a booking
     */
    public function checkIn(Booking $booking)
    {
        if ($booking->checkIn()) {
            return back()->with('success', 'Guest checked in successfully.');
        }

        return back()->with('error', 'Unable to check in this booking.');
    }

    /**
     * Check out a booking
     */
    public function checkOut(Booking $booking)
    {
        if ($booking->checkOut()) {
            return back()->with('success', 'Guest checked out successfully.');
        }

        return back()->with('error', 'Unable to check out this booking.');
    }
}
