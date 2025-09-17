<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with('roomCategory')
            ->orderBy('room_number')
            ->paginate(20);

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = RoomCategory::active()->get();
        return view('admin.rooms.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_category_id' => 'required|exists:room_categories,id',
            'room_number' => 'required|string|max:255|unique:rooms',
            'floor' => 'required|integer|min:1|max:20',
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        Room::create($request->all());

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $room->load(['roomCategory', 'images', 'bookings.customer.user']);
        return view('admin.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        $categories = RoomCategory::active()->get();
        return view('admin.rooms.edit', compact('room', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_category_id' => 'required|exists:room_categories,id',
            'room_number' => 'required|string|max:255|unique:rooms,room_number,' . $room->id,
            'floor' => 'required|integer|min:1|max:20',
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $room->update($request->all());

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        // Check if room has active bookings
        $activeBookings = $room->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->exists();

        if ($activeBookings) {
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Cannot delete room with active bookings.');
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}
