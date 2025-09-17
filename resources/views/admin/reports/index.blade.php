<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Bookings Report -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Bookings Report</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Generate detailed booking reports with filters</p>

                        <form method="POST" action="{{ route('admin.reports.bookings') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label for="bookings_date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Date</label>
                                <input type="date" name="date_from" id="bookings_date_from" required
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="bookings_date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Date</label>
                                <input type="date" name="date_to" id="bookings_date_to" required
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="bookings_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select name="status" id="bookings_status"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="checked_in">Checked In</option>
                                    <option value="checked_out">Checked Out</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label for="bookings_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Format</label>
                                <select name="format" id="bookings_format" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="csv">CSV</option>
                                    <option value="pdf">PDF</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Generate Bookings Report
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Occupancy Report -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Occupancy Report</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Track room occupancy rates over time</p>

                        <form method="POST" action="{{ route('admin.reports.occupancy') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label for="occupancy_period" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Period</label>
                                <select name="period" id="occupancy_period" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>

                            <div>
                                <label for="occupancy_date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Date</label>
                                <input type="date" name="date_from" id="occupancy_date_from" required
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="occupancy_date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Date</label>
                                <input type="date" name="date_to" id="occupancy_date_to" required
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="occupancy_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Format</label>
                                <select name="format" id="occupancy_format" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="csv">CSV</option>
                                    <option value="pdf">PDF</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Generate Occupancy Report
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Revenue Report -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Revenue Report</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Analyze revenue by different metrics</p>

                        <form method="POST" action="{{ route('admin.reports.revenue') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label for="revenue_group_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Group By</label>
                                <select name="group_by" id="revenue_group_by" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="room_category">Room Category</option>
                                </select>
                            </div>

                            <div>
                                <label for="revenue_date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Date</label>
                                <input type="date" name="date_from" id="revenue_date_from" required
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="revenue_date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Date</label>
                                <input type="date" name="date_to" id="revenue_date_to" required
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="revenue_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Format</label>
                                <select name="format" id="revenue_format" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="csv">CSV</option>
                                    <option value="pdf">PDF</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                Generate Revenue Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Quick Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ \App\Models\Booking::count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Bookings</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format(\App\Models\Booking::sum('total_amount'), 2) }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ \App\Models\Room::active()->count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Rooms</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ \App\Models\User::where('role', 'customer')->count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Customers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
