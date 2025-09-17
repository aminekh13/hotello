<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomController extends Controller
{
    /**
     * Display a listing of available rooms
     */
    public function index(Request $request)
    {
        $query = Room::with(['roomCategory', 'hotel'])->active()->available();

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('room_category_id', $request->category_id);
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        $rooms = $query->orderBy('price_per_night')->paginate(12);
        $categories = RoomCategory::active()->get();
        $floors = Room::active()->distinct()->pluck('floor')->sort()->values();

        return view('rooms.index', compact('rooms', 'categories', 'floors'));
    }

    /**
     * Search for available rooms by date and guests
     */
    public function search(Request $request)
    {
        $request->validate([
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
        ]);

        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);

        $rooms = Room::with(['roomCategory', 'hotel'])
            ->active()
            ->available()
            ->where('is_available', true)
            ->get()
            ->filter(function ($room) use ($checkInDate, $checkOutDate, $request) {
                return $room->isAvailableForDates($checkInDate, $checkOutDate) &&
                       $room->roomCategory->max_occupancy >= $request->guests;
            })
            ->sortBy('price_per_night');

        $categories = RoomCategory::active()->get();

        return view('rooms.search', compact('rooms', 'checkInDate', 'checkOutDate', 'request'));
    }

    /**
     * Display the specified room
     */
    public function show(Room $room)
    {
        $room->load(['roomCategory', 'hotel', 'images']);

        if (!$room->is_active) {
            abort(404, 'Room not found.');
        }

        // Get similar rooms
        $similarRooms = Room::with(['roomCategory', 'hotel'])
            ->where('room_category_id', $room->room_category_id)
            ->where('id', '!=', $room->id)
            ->active()
            ->available()
            ->limit(4)
            ->get();

        return view('rooms.show', compact('room', 'similarRooms'));
    }
}
