<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Search Rooms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('rooms.search') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Check-in Date -->
                            <div>
                                <label for="check_in_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-in Date</label>
                                <input type="date" name="check_in_date" id="check_in_date"
                                    value="{{ request('check_in_date') }}"
                                    min="{{ date('Y-m-d') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Check-out Date -->
                            <div>
                                <label for="check_out_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-out Date</label>
                                <input type="date" name="check_out_date" id="check_out_date"
                                    value="{{ request('check_out_date') }}"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Number of Guests -->
                            <div>
                                <label for="guests" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of Guests</label>
                                <select name="guests" id="guests" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @for($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ request('guests') == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Search Button -->
                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Search Rooms
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            @if(request()->has('check_in_date') && request()->has('check_out_date'))
                @php
                    $checkIn = request('check_in_date');
                    $checkOut = request('check_out_date');
                    $guests = request('guests', 1);
                @endphp

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4">
                            Available Rooms
                            @if($checkIn && $checkOut)
                                from {{ \Carbon\Carbon::parse($checkIn)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($checkOut)->format('M d, Y') }}
                            @endif
                        </h3>

                        @if(isset($rooms) && $rooms->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($rooms as $room)
                                    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden shadow-sm">
                                        <!-- Room Image Placeholder -->
                                        <div class="h-48 bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <span class="text-gray-500 dark:text-gray-400">Room Image</span>
                                        </div>

                                        <div class="p-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $room->room_number }}
                                                </h4>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    Floor {{ $room->floor }}
                                                </span>
                                            </div>

                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                {{ $room->roomCategory->name }}
                                            </p>

                                            @if($room->hotel)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                    {{ $room->hotel->name }}
                                                </p>
                                            @endif

                                            @if($room->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                                    {{ Str::limit($room->description, 100) }}
                                                </p>
                                            @endif

                                            <div class="flex justify-between items-center mb-3">
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    Max {{ $room->roomCategory->max_occupancy }} guests
                                                </div>
                                                <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                                    ${{ number_format($room->price_per_night, 2) }}/night
                                                </div>
                                            </div>

                                            <!-- Amenities -->
                                            @if($room->roomCategory->amenities && count($room->roomCategory->amenities) > 0)
                                                <div class="mb-3">
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach(array_slice($room->roomCategory->amenities, 0, 3) as $amenity)
                                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs rounded">
                                                                {{ $amenity }}
                                                            </span>
                                                        @endforeach
                                                        @if(count($room->roomCategory->amenities) > 3)
                                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300 text-xs rounded">
                                                                +{{ count($room->roomCategory->amenities) - 3 }} more
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Book Now Button -->
                                            <div class="flex space-x-2">
                                                <a href="{{ route('rooms.show', $room) }}"
                                                   class="flex-1 bg-gray-500 hover:bg-gray-700 text-white text-center font-bold py-2 px-4 rounded text-sm">
                                                    View Details
                                                </a>
                                                <a href="{{ route('bookings.create', [
                                                    'room_id' => $room->id,
                                                    'check_in_date' => $checkIn,
                                                    'check_out_date' => $checkOut,
                                                    'guests' => $guests
                                                ]) }}"
                                                   class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center font-bold py-2 px-4 rounded text-sm">
                                                    Book Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-gray-500 dark:text-gray-400 mb-4">
                                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No rooms available</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">
                                    Sorry, no rooms are available for your selected dates and guest count.
                                </p>
                                <a href="{{ route('rooms.index') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    Browse all rooms
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Set minimum check-out date based on check-in date
        document.getElementById('check_in_date').addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            const checkOutDate = new Date(checkInDate);
            checkOutDate.setDate(checkOutDate.getDate() + 1);

            const checkOutInput = document.getElementById('check_out_date');
            checkOutInput.min = checkOutDate.toISOString().split('T')[0];

            // If current check-out date is before new minimum, update it
            if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
                checkOutInput.value = checkOutDate.toISOString().split('T')[0];
            }
        });
    </script>
</x-app-layout>
