<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $hotel->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.hotels.edit', $hotel) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Edit Hotel
                </a>
                <a href="{{ route('admin.hotels.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Hotels
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hotel Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Basic Info -->
                        <div>
                            <h3 class="text-lg font-medium mb-4">Hotel Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-medium">Name:</span> {{ $hotel->name }}
                                </div>
                                <div>
                                    <span class="font-medium">Rating:</span>
                                    <div class="flex items-center mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $hotel->star_rating)
                                                <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <div>
                                    <span class="font-medium">Status:</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($hotel->is_active) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        {{ $hotel->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                @if($hotel->description)
                                <div>
                                    <span class="font-medium">Description:</span>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $hotel->description }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Contact & Location -->
                        <div>
                            <h3 class="text-lg font-medium mb-4">Contact & Location</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-medium">Address:</span> {{ $hotel->full_address }}
                                </div>
                                @if($hotel->phone)
                                <div>
                                    <span class="font-medium">Phone:</span> {{ $hotel->phone }}
                                </div>
                                @endif
                                @if($hotel->email)
                                <div>
                                    <span class="font-medium">Email:</span> {{ $hotel->email }}
                                </div>
                                @endif
                                @if($hotel->website)
                                <div>
                                    <span class="font-medium">Website:</span>
                                    <a href="{{ $hotel->website }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">{{ $hotel->website }}</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    @if($hotel->amenities && count($hotel->amenities) > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium mb-4">Amenities</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($hotel->amenities as $amenity)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-sm rounded-full">
                                    {{ $amenity }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Room Management -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Room Management</h3>
                        <div class="flex space-x-2">
                            <button onclick="toggleRoomForm()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Add New Room
                            </button>
                            <button onclick="toggleAssignForm()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Assign Existing Room
                            </button>
                        </div>
                    </div>

                    <!-- Add New Room Form (Hidden by default) -->
                    <div id="roomForm" class="hidden mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="text-md font-medium mb-4">Create New Room</h4>
                        <form method="POST" action="{{ route('admin.hotels.create-room', $hotel) }}">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="room_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room Category</label>
                                    <select name="room_category_id" id="room_category_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select Category</option>
                                        @foreach($roomCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="room_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room Number</label>
                                    <input type="text" name="room_number" id="room_number" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="floor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Floor</label>
                                    <input type="number" name="floor" id="floor" min="1" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="price_per_night" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price per Night</label>
                                    <input type="number" name="price_per_night" id="price_per_night" step="0.01" min="0" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                    <input type="text" name="description" id="description"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_available" value="1" checked
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Available</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <button type="button" onclick="toggleRoomForm()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Create Room
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Assign Existing Room Form (Hidden by default) -->
                    <div id="assignForm" class="hidden mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="text-md font-medium mb-4">Assign Existing Room</h4>
                        <form method="POST" action="{{ route('admin.hotels.assign-room', $hotel) }}">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="room_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Room</label>
                                    <select name="room_id" id="room_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select Room</option>
                                        @php
                                            $unassignedRooms = \App\Models\Room::whereNull('hotel_id')->active()->get();
                                        @endphp
                                        @foreach($unassignedRooms as $room)
                                            <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->roomCategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <button type="button" onclick="toggleAssignForm()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Assign Room
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Rooms List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Room Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Floor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price/Night</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($hotel->rooms as $room)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $room->room_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $room->roomCategory->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $room->floor }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${{ number_format($room->price_per_night, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($room->is_active) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @endif">
                                                {{ $room->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($room->is_available) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                @endif">
                                                {{ $room->is_available ? 'Available' : 'Occupied' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.rooms.edit', $room) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No rooms assigned to this hotel</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleRoomForm() {
            const form = document.getElementById('roomForm');
            const assignForm = document.getElementById('assignForm');
            form.classList.toggle('hidden');
            assignForm.classList.add('hidden');
        }

        function toggleAssignForm() {
            const form = document.getElementById('assignForm');
            const roomForm = document.getElementById('roomForm');
            form.classList.toggle('hidden');
            roomForm.classList.add('hidden');
        }
    </script>
</x-app-layout>
