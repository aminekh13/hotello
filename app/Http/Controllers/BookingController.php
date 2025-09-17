<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAgent()) {
            $bookings = Booking::with(['customer.user', 'room.roomCategory', 'agent'])
                ->where('agent_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            // Customer viewing their own bookings
            $customer = $user->customer;
            if (!$customer) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Please complete your customer profile first.');
            }

            $bookings = Booking::with(['customer.user', 'room.roomCategory', 'agent'])
                ->where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $checkIn = $request->get('check_in_date');
        $checkOut = $request->get('check_out_date');
        $guests = $request->get('guests', 1);
        $roomId = $request->get('room_id');

        $rooms = collect();
        if ($checkIn && $checkOut) {
            $checkInDate = Carbon::parse($checkIn);
            $checkOutDate = Carbon::parse($checkOut);

            if ($checkInDate->isFuture() && $checkOutDate->gt($checkInDate)) {
                $rooms = Room::with('roomCategory')
                    ->active()
                    ->available()
                    ->where('is_available', true)
                    ->get()
                    ->filter(function ($room) use ($checkInDate, $checkOutDate, $guests) {
                        return $room->isAvailableForDates($checkInDate, $checkOutDate) &&
                               $room->roomCategory->max_occupancy >= $guests;
                    });
            }
        }

        return view('bookings.create', compact('rooms', 'checkIn', 'checkOut', 'guests', 'roomId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
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

        DB::beginTransaction();
        try {
            // Create or find customer
            $user = User::where('email', $request->customer_email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'password' => bcrypt('temporary_password_' . uniqid()),
                    'role' => 'customer',
                    'phone' => $request->customer_phone,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            }

            $customer = $user->customer;
            if (!$customer) {
                $customer = Customer::create([
                    'user_id' => $user->id,
                    'phone' => $request->customer_phone,
                    'is_active' => true,
                ]);
            }

            // Calculate total amount
            $nights = $checkInDate->diffInDays($checkOutDate);
            $totalAmount = $room->price_per_night * $nights;

            // Create booking
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'room_id' => $room->id,
                'agent_id' => $user?->isAgent() ? $user->id : null,
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
                'guests' => $request->guests,
                'total_amount' => $totalAmount,
                'special_requests' => $request->special_requests,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking created successfully! Reference: ' . $booking->booking_reference);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create booking. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $booking->load(['customer.user', 'room.roomCategory', 'agent']);

        // Check if user can view this booking
        $user = auth()->user();
        if ($user) {
            if ($user->isAdmin() || $user->isStaff()) {
                // Admin/Staff can view all bookings
            } elseif ($user->isAgent() && $booking->agent_id === $user->id) {
                // Agent can view their own bookings
            } elseif ($user->isCustomer() && $booking->customer_id === $user->customer?->id) {
                // Customer can view their own bookings
            } else {
                abort(403, 'Unauthorized access to this booking.');
            }
        } else {
            // Public access - only show basic info
            if ($booking->status === 'cancelled') {
                abort(404, 'Booking not found.');
            }
        }

        return view('bookings.show', compact('booking'));
    }

    /**
     * Cancel a booking
     */
    public function cancel(Request $request, Booking $booking)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        if (!$user) {
            abort(403, 'You must be logged in to cancel a booking.');
        }

        // Check if user can cancel this booking
        if ($user->isAdmin() || $user->isStaff()) {
            // Admin/Staff can cancel any booking
        } elseif ($user->isAgent() && $booking->agent_id === $user->id) {
            // Agent can cancel their own bookings
        } elseif ($user->isCustomer() && $booking->customer_id === $user->customer?->id) {
            // Customer can cancel their own bookings
        } else {
            abort(403, 'Unauthorized access to cancel this booking.');
        }

        if (!$booking->canBeCancelled()) {
            return back()->withErrors(['error' => 'This booking cannot be cancelled.']);
        }

        $booking->cancel($request->cancellation_reason);

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
