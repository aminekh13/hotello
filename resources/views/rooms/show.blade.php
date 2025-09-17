<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $room->room_number }} - {{ $room->roomCategory->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Room Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <!-- Room Images -->
                            <div class="mb-6">
                                <div class="h-64 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-400">Room Image Placeholder</span>
                                </div>
                            </div>

                            <!-- Room Information -->
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $room->room_number }} - {{ $room->roomCategory->name }}
                                </h3>

                                @if($room->hotel)
                                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-4">
                                        {{ $room->hotel->name }}
                                    </p>
                                @endif

                                @if($room->description)
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        {{ $room->description }}
                                    </p>
                                @endif

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">Floor:</span>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $room->floor }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">Max Occupancy:</span>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $room->roomCategory->max_occupancy }} guests</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Amenities -->
                            @if($room->roomCategory->amenities && count($room->roomCategory->amenities) > 0)
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Amenities</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        @foreach($room->roomCategory->amenities as $amenity)
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $amenity }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Hotel Information -->
                            @if($room->hotel)
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Hotel Information</h4>
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Address:</span>
                                                <p class="text-gray-600 dark:text-gray-400">{{ $room->hotel->full_address }}</p>
                                            </div>
                                            @if($room->hotel->phone)
                                                <div>
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Phone:</span>
                                                    <p class="text-gray-600 dark:text-gray-400">{{ $room->hotel->phone }}</p>
                                                </div>
                                            @endif
                                            @if($room->hotel->email)
                                                <div>
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Email:</span>
                                                    <p class="text-gray-600 dark:text-gray-400">{{ $room->hotel->email }}</p>
                                                </div>
                                            @endif
                                            @if($room->hotel->website)
                                                <div>
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Website:</span>
                                                    <p class="text-gray-600 dark:text-gray-400">
                                                        <a href="{{ $room->hotel->website }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                            {{ $room->hotel->website }}
                                                        </a>
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                        @if($room->hotel->amenities && count($room->hotel->amenities) > 0)
                                            <div class="mt-4">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Hotel Amenities:</span>
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    @foreach($room->hotel->amenities as $amenity)
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs rounded">
                                                            {{ $amenity }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Booking Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="text-center mb-6">
                                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    ${{ number_format($room->price_per_night, 2) }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">per night</div>
                            </div>

                            <!-- Quick Booking Form -->
                            <form method="GET" action="{{ route('bookings.create') }}" class="space-y-4">
                                <input type="hidden" name="room_id" value="{{ $room->id }}">

                                <div>
                                    <label for="check_in_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Check-in Date</label>
                                    <input type="date" name="check_in_date" id="check_in_date"
                                        value="{{ request('check_in_date') }}"
                                        min="{{ date('Y-m-d') }}" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="check_out_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Check-out Date</label>
                                    <input type="date" name="check_out_date" id="check_out_date"
                                        value="{{ request('check_out_date') }}"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="guests" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Number of Guests</label>
                                    <select name="guests" id="guests" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @for($i = 1; $i <= $room->roomCategory->max_occupancy; $i++)
                                            <option value="{{ $i }}" {{ request('guests') == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded">
                                    Book This Room
                                </button>
                            </form>

                            <!-- Room Status -->
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($room->is_available) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        {{ $room->is_available ? 'Available' : 'Occupied' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar Rooms -->
            @if(isset($similarRooms) && $similarRooms->count() > 0)
                <div class="mt-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-medium mb-4">Similar Rooms</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($similarRooms as $similarRoom)
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                        <div class="h-32 bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <span class="text-gray-500 dark:text-gray-400 text-sm">Room Image</span>
                                        </div>
                                        <div class="p-3">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $similarRoom->room_number }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $similarRoom->roomCategory->name }}</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${{ number_format($similarRoom->price_per_night, 2) }}/night</p>
                                            <a href="{{ route('rooms.show', $similarRoom) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
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
