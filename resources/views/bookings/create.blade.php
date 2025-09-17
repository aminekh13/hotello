<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Book a Room') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Booking Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Booking Information</h3>

                            <form method="POST" action="{{ route('bookings.store') }}">
                                @csrf

                                <!-- Room Selection -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Room</label>
                                    @if($rooms->count() > 0)
                                        <div class="space-y-4">
                                            @foreach($rooms as $room)
                                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer room-option"
                                                     data-room-id="{{ $room->id }}"
                                                     data-price="{{ $room->price_per_night }}"
                                                     data-room-number="{{ $room->room_number }}"
                                                     data-category="{{ $room->roomCategory->name }}">
                                                    <div class="flex items-center">
                                                        <input type="radio" name="room_id" value="{{ $room->id }}"
                                                               id="room_{{ $room->id }}"
                                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                               {{ $roomId == $room->id ? 'checked' : '' }}>
                                                        <label for="room_{{ $room->id }}" class="ml-3 flex-1 cursor-pointer">
                                                            <div class="flex justify-between items-center">
                                                                <div>
                                                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $room->room_number }}</h4>
                                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $room->roomCategory->name }}</p>
                                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Floor {{ $room->floor }} • Up to {{ $room->roomCategory->max_occupancy }} guests</p>
                                                                </div>
                                                                <div class="text-right">
                                                                    <p class="text-xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($room->price_per_night, 2) }}</p>
                                                                    <p class="text-sm text-gray-500 dark:text-gray-400">per night</p>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No rooms available</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No rooms are available for the selected dates and guest count.</p>
                                            <a href="{{ route('rooms.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                                Search Different Dates
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                @if($rooms->count() > 0)
                                    <!-- Dates and Guests -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                        <div>
                                            <label for="check_in_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-in Date</label>
                                            <input type="date" name="check_in_date" id="check_in_date"
                                                   value="{{ $checkIn }}"
                                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        </div>
                                        <div>
                                            <label for="check_out_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Check-out Date</label>
                                            <input type="date" name="check_out_date" id="check_out_date"
                                                   value="{{ $checkOut }}"
                                                   min="{{ date('Y-m-d', strtotime('+2 days')) }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        </div>
                                        <div>
                                            <label for="guests" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of Guests</label>
                                            <input type="number" name="guests" id="guests" value="{{ $guests }}" min="1" max="6"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        </div>
                                    </div>

                                    <!-- Customer Information -->
                                    <div class="mb-6">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Guest Information</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                                                <input type="text" name="customer_name" id="customer_name"
                                                       value="{{ old('customer_name') }}"
                                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                @error('customer_name')
                                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="customer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                                <input type="email" name="customer_email" id="customer_email"
                                                       value="{{ old('customer_email') }}"
                                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                @error('customer_email')
                                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="md:col-span-2">
                                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                                                <input type="tel" name="customer_phone" id="customer_phone"
                                                       value="{{ old('customer_phone') }}"
                                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                @error('customer_phone')
                                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Special Requests -->
                                    <div class="mb-6">
                                        <label for="special_requests" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Special Requests</label>
                                        <textarea name="special_requests" id="special_requests" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                  placeholder="Any special requests or notes...">{{ old('special_requests') }}</textarea>
                                        @error('special_requests')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg">
                                            Complete Booking
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Booking Summary</h3>

                            <div id="booking-summary" class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Check-in:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100" id="summary-checkin">{{ $checkIn ? date('M d, Y', strtotime($checkIn)) : 'Select dates' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Check-out:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100" id="summary-checkout">{{ $checkOut ? date('M d, Y', strtotime($checkOut)) : 'Select dates' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Guests:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100" id="summary-guests">{{ $guests }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Nights:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100" id="summary-nights">-</span>
                                </div>
                                <hr class="border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Room:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100" id="summary-room">Select room</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Price per night:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100" id="summary-price">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100" id="summary-total">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkInInput = document.getElementById('check_in_date');
            const checkOutInput = document.getElementById('check_out_date');
            const guestsInput = document.getElementById('guests');
            const roomOptions = document.querySelectorAll('.room-option');

            function updateSummary() {
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;
                const guests = guestsInput.value;

                document.getElementById('summary-checkin').textContent = checkIn ? new Date(checkIn).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Select dates';
                document.getElementById('summary-checkout').textContent = checkOut ? new Date(checkOut).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Select dates';
                document.getElementById('summary-guests').textContent = guests;

                if (checkIn && checkOut) {
                    const nights = Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
                    document.getElementById('summary-nights').textContent = nights;
                } else {
                    document.getElementById('summary-nights').textContent = '-';
                }

                const selectedRoom = document.querySelector('input[name="room_id"]:checked');
                if (selectedRoom) {
                    const roomOption = selectedRoom.closest('.room-option');
                    const roomNumber = roomOption.dataset.roomNumber;
                    const category = roomOption.dataset.category;
                    const price = parseFloat(roomOption.dataset.price);

                    document.getElementById('summary-room').textContent = `${roomNumber} (${category})`;
                    document.getElementById('summary-price').textContent = `$${price.toFixed(2)}`;

                    if (checkIn && checkOut) {
                        const nights = Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
                        const total = price * nights;
                        document.getElementById('summary-total').textContent = `$${total.toFixed(2)}`;
                    } else {
                        document.getElementById('summary-total').textContent = '-';
                    }
                } else {
                    document.getElementById('summary-room').textContent = 'Select room';
                    document.getElementById('summary-price').textContent = '-';
                    document.getElementById('summary-total').textContent = '-';
                }
            }

            checkInInput.addEventListener('change', updateSummary);
            checkOutInput.addEventListener('change', updateSummary);
            guestsInput.addEventListener('change', updateSummary);

            roomOptions.forEach(option => {
                const radio = option.querySelector('input[type="radio"]');
                radio.addEventListener('change', updateSummary);
            });

            // Initial update
            updateSummary();
        });
    </script>
</x-app-layout>
