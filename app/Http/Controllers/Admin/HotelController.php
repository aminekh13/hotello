<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hotels = Hotel::withCount(['rooms', 'activeRooms', 'availableRooms'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.hotels.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.hotels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'star_rating' => 'required|integer|between:1,5',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'is_active' => 'boolean',
        ]);

        $hotel = Hotel::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => $request->website,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'star_rating' => $request->star_rating,
            'amenities' => $request->amenities,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.hotels.show', $hotel)
            ->with('success', 'Hotel created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel)
    {
        $hotel->load(['rooms.roomCategory', 'rooms.images']);

        $roomCategories = RoomCategory::active()->get();

        return view('admin.hotels.show', compact('hotel', 'roomCategories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hotel $hotel)
    {
        return view('admin.hotels.edit', compact('hotel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'star_rating' => 'required|integer|between:1,5',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'is_active' => 'boolean',
        ]);

        $hotel->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => $request->website,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'star_rating' => $request->star_rating,
            'amenities' => $request->amenities,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.hotels.show', $hotel)
            ->with('success', 'Hotel updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        // Check if hotel has active bookings
        $activeBookings = $hotel->rooms()
            ->whereHas('bookings', function ($query) {
                $query->whereNotIn('status', ['cancelled', 'checked_out']);
            })
            ->exists();

        if ($activeBookings) {
            return back()->withErrors(['error' => 'Cannot delete hotel with active bookings.']);
        }

        $hotel->delete();

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel deleted successfully!');
    }

    /**
     * Create a new room for the hotel
     */
    public function createRoom(Request $request, Hotel $hotel)
    {
        $request->validate([
            'room_category_id' => 'required|exists:room_categories,id',
            'room_number' => 'required|string|max:20|unique:rooms,room_number',
            'floor' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $room = $hotel->rooms()->create([
            'room_category_id' => $request->room_category_id,
            'room_number' => $request->room_number,
            'floor' => $request->floor,
            'description' => $request->description,
            'price_per_night' => $request->price_per_night,
            'is_available' => $request->boolean('is_available', true),
            'is_active' => true,
        ]);

        return back()->with('success', 'Room created successfully!');
    }

    /**
     * Assign existing room to hotel
     */
    public function assignRoom(Request $request, Hotel $hotel)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($room->hotel_id) {
            return back()->withErrors(['error' => 'Room is already assigned to another hotel.']);
        }

        $room->update(['hotel_id' => $hotel->id]);

        return back()->with('success', 'Room assigned successfully!');
    }
}
