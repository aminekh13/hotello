<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Our Rooms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Search Available Rooms</h3>
                    <form method="GET" action="{{ route('rooms.search') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="check_in_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-in Date</label>
                            <input type="date" name="check_in_date" id="check_in_date"
                                   value="{{ request('check_in_date', date('Y-m-d', strtotime('+1 day'))) }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label for="check_out_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-out Date</label>
                            <input type="date" name="check_out_date" id="check_out_date"
                                   value="{{ request('check_out_date', date('Y-m-d', strtotime('+2 days'))) }}"
                                   min="{{ date('Y-m-d', strtotime('+2 days')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label for="guests" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Guests</label>
                            <select name="guests" id="guests" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ request('guests', 1) == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Search Rooms
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Filter Rooms</h3>
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room Type</label>
                            <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Types</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="min_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min Price ($)</label>
                            <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}" min="0" step="10"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="max_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Price ($)</label>
                            <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" min="0" step="10"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Apply Filters
                            </button>
                            <a href="{{ route('rooms.index') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Rooms Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($rooms as $room)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $room->room_number }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $room->roomCategory->name }}</p>
                                @if($room->hotel)
                                    <p class="text-xs text-gray-500 dark:text-gray-500">{{ $room->hotel->name }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($room->price_per_night, 2) }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">per night</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $room->description }}</p>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Floor {{ $room->floor }}
                            </div>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Up to {{ $room->roomCategory->max_occupancy }} guests
                            </div>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Amenities</h4>
                            <div class="flex flex-wrap gap-1">
                                @foreach($room->roomCategory->amenities as $amenity)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $amenity }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ route('rooms.show', $room) }}" class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                                View Details
                            </a>
                            <a href="{{ route('bookings.create', ['room_id' => $room->id]) }}" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No rooms found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters or search criteria.</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($rooms->hasPages())
                <div class="mt-8">
                    {{ $rooms->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
