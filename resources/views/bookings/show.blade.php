<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Booking Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Booking Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Booking {{ $booking->booking_reference }}</h1>
                            <p class="text-gray-600 dark:text-gray-400">Created on {{ $booking->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($booking->status === 'confirmed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($booking->status === 'checked_out') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Booking Details -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Booking Information</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Booking Reference:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->booking_reference }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Check-in Date:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->check_in_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Check-out Date:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->check_out_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Number of Nights:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->nights }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Number of Guests:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->guests }}</span>
                                </div>
                                @if($booking->special_requests)
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Special Requests:</span>
                                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $booking->special_requests }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Room Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Room Information</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Room Number:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->room->room_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Room Type:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->room->roomCategory->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Floor:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->room->floor }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Price per Night:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">${{ number_format($booking->room->price_per_night, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Max Occupancy:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->room->roomCategory->max_occupancy }} guests</span>
                                </div>
                            </div>

                            <!-- Amenities -->
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Amenities</h4>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($booking->room->roomCategory->amenities as $amenity)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $amenity }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Guest Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->customer->user->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Email:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->customer->user->email }}</span>
                            </div>
                            @if($booking->customer->phone)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->customer->phone }}</span>
                                </div>
                            @endif
                            @if($booking->agent)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Booking Agent:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->agent->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Payment Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Total Amount:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">${{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Paid Amount:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">${{ number_format($booking->paid_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Remaining Amount:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">${{ number_format($booking->remaining_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Payment Status:</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($booking->payment_status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($booking->payment_status === 'partial') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($booking->payment_status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Cancellation Information -->
                    @if($booking->status === 'cancelled')
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Cancellation Information</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Cancelled On:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->cancelled_at->format('M d, Y \a\t g:i A') }}</span>
                                </div>
                                @if($booking->cancellation_reason)
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Reason:</span>
                                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $booking->cancellation_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="mt-8 flex justify-between">
                        <div>
                            @if($booking->canBeCancelled())
                                <button onclick="toggleCancellationForm()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Cancel Booking
                                </button>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('rooms.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Book Another Room
                            </a>
                        </div>
                    </div>

                    <!-- Cancellation Form (Hidden by default) -->
                    @if($booking->canBeCancelled())
                        <div id="cancellation-form" class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hidden">
                            <h4 class="text-lg font-medium text-red-800 dark:text-red-200 mb-4">Cancel Booking</h4>
                            <form method="POST" action="{{ route('bookings.cancel', $booking) }}">
                                @csrf
                                <div class="mb-4">
                                    <label for="cancellation_reason" class="block text-sm font-medium text-red-700 dark:text-red-300">Reason for Cancellation</label>
                                    <textarea name="cancellation_reason" id="cancellation_reason" rows="3"
                                              class="mt-1 block w-full rounded-md border-red-300 dark:border-red-600 dark:bg-red-900/50 dark:text-red-200 shadow-sm focus:border-red-500 focus:ring-red-500"
                                              placeholder="Please provide a reason for cancelling this booking..."></textarea>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Confirm Cancellation
                                    </button>
                                    <button type="button" onclick="toggleCancellationForm()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCancellationForm() {
            const form = document.getElementById('cancellation-form');
            form.classList.toggle('hidden');
        }
    </script>
</x-app-layout>
