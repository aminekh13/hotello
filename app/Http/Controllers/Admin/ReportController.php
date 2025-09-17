<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports index page
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Generate bookings report
     */
    public function bookings(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,pdf',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'status' => 'nullable|in:pending,confirmed,checked_in,checked_out,cancelled',
            'agent_id' => 'nullable|exists:users,id',
        ]);

        $query = Booking::with(['customer.user', 'room.roomCategory', 'agent']);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('check_in_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('check_out_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        if ($request->format === 'csv') {
            return $this->generateBookingsCsv($bookings, $request);
        } else {
            return $this->generateBookingsPdf($bookings, $request);
        }
    }

    /**
     * Generate occupancy report
     */
    public function occupancy(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,pdf',
            'period' => 'required|in:daily,monthly',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        if ($request->period === 'daily') {
            $data = $this->generateDailyOccupancyData($dateFrom, $dateTo);
        } else {
            $data = $this->generateMonthlyOccupancyData($dateFrom, $dateTo);
        }

        if ($request->format === 'csv') {
            return $this->generateOccupancyCsv($data, $request);
        } else {
            return $this->generateOccupancyPdf($data, $request);
        }
    }

    /**
     * Generate revenue report
     */
    public function revenue(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,pdf',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'group_by' => 'required|in:daily,monthly,room_category',
        ]);

        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        $data = $this->generateRevenueData($dateFrom, $dateTo, $request->group_by);

        if ($request->format === 'csv') {
            return $this->generateRevenueCsv($data, $request);
        } else {
            return $this->generateRevenuePdf($data, $request);
        }
    }

    /**
     * Generate bookings CSV
     */
    private function generateBookingsCsv($bookings, $request)
    {
        $filename = 'bookings_report_' . $request->date_from . '_to_' . $request->date_to . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Booking Reference',
                'Customer Name',
                'Customer Email',
                'Room Number',
                'Room Type',
                'Check-in Date',
                'Check-out Date',
                'Nights',
                'Guests',
                'Total Amount',
                'Paid Amount',
                'Status',
                'Payment Status',
                'Agent',
                'Created At'
            ]);

            // CSV Data
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_reference,
                    $booking->customer->user->name,
                    $booking->customer->user->email,
                    $booking->room->room_number,
                    $booking->room->roomCategory->name,
                    $booking->check_in_date->format('Y-m-d'),
                    $booking->check_out_date->format('Y-m-d'),
                    $booking->nights,
                    $booking->guests,
                    $booking->total_amount,
                    $booking->paid_amount,
                    ucfirst(str_replace('_', ' ', $booking->status)),
                    ucfirst($booking->payment_status),
                    $booking->agent ? $booking->agent->name : 'Direct',
                    $booking->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate bookings PDF
     */
    private function generateBookingsPdf($bookings, $request)
    {
        // For now, return a simple HTML response
        // In a real application, you would use a PDF library like DomPDF or TCPDF
        $html = view('admin.reports.bookings-pdf', compact('bookings', 'request'))->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="bookings_report.html"');
    }

    /**
     * Generate daily occupancy data
     */
    private function generateDailyOccupancyData($dateFrom, $dateTo)
    {
        $data = [];
        $totalRooms = Room::active()->count();

        for ($date = $dateFrom->copy(); $date->lte($dateTo); $date->addDay()) {
            $occupiedRooms = Booking::where('check_in_date', '<=', $date)
                ->where('check_out_date', '>', $date)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->count();

            $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'occupied_rooms' => $occupiedRooms,
                'total_rooms' => $totalRooms,
                'occupancy_rate' => round($occupancyRate, 2),
            ];
        }

        return $data;
    }

    /**
     * Generate monthly occupancy data
     */
    private function generateMonthlyOccupancyData($dateFrom, $dateTo)
    {
        $data = [];
        $totalRooms = Room::active()->count();

        for ($date = $dateFrom->copy()->startOfMonth(); $date->lte($dateTo); $date->addMonth()) {
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $occupiedRooms = Booking::where('check_in_date', '<=', $monthEnd)
                ->where('check_out_date', '>', $monthStart)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->count();

            $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

            $data[] = [
                'month' => $date->format('Y-m'),
                'occupied_rooms' => $occupiedRooms,
                'total_rooms' => $totalRooms,
                'occupancy_rate' => round($occupancyRate, 2),
            ];
        }

        return $data;
    }

    /**
     * Generate revenue data
     */
    private function generateRevenueData($dateFrom, $dateTo, $groupBy)
    {
        $query = Booking::where('check_in_date', '>=', $dateFrom)
            ->where('check_out_date', '<=', $dateTo)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out']);

        if ($groupBy === 'room_category') {
            return $query->with('room.roomCategory')
                ->get()
                ->groupBy('room.roomCategory.name')
                ->map(function ($bookings, $category) {
                    return [
                        'category' => $category,
                        'total_bookings' => $bookings->count(),
                        'total_revenue' => $bookings->sum('total_amount'),
                        'average_revenue' => $bookings->avg('total_amount'),
                    ];
                })
                ->values()
                ->toArray();
        }

        // For daily/monthly grouping, we'll use a simplified approach
        $bookings = $query->get();

        if ($groupBy === 'daily') {
            return $bookings->groupBy(function ($booking) {
                return $booking->check_in_date->format('Y-m-d');
            })->map(function ($dayBookings, $date) {
                return [
                    'date' => $date,
                    'total_bookings' => $dayBookings->count(),
                    'total_revenue' => $dayBookings->sum('total_amount'),
                ];
            })->values()->toArray();
        } else {
            return $bookings->groupBy(function ($booking) {
                return $booking->check_in_date->format('Y-m');
            })->map(function ($monthBookings, $month) {
                return [
                    'month' => $month,
                    'total_bookings' => $monthBookings->count(),
                    'total_revenue' => $monthBookings->sum('total_amount'),
                ];
            })->values()->toArray();
        }
    }

    /**
     * Generate occupancy CSV
     */
    private function generateOccupancyCsv($data, $request)
    {
        $filename = 'occupancy_report_' . $request->period . '_' . $request->date_from . '_to_' . $request->date_to . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $request) {
            $file = fopen('php://output', 'w');

            if ($request->period === 'daily') {
                fputcsv($file, ['Date', 'Occupied Rooms', 'Total Rooms', 'Occupancy Rate (%)']);
                foreach ($data as $row) {
                    fputcsv($file, [$row['date'], $row['occupied_rooms'], $row['total_rooms'], $row['occupancy_rate']]);
                }
            } else {
                fputcsv($file, ['Month', 'Occupied Rooms', 'Total Rooms', 'Occupancy Rate (%)']);
                foreach ($data as $row) {
                    fputcsv($file, [$row['month'], $row['occupied_rooms'], $row['total_rooms'], $row['occupancy_rate']]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate occupancy PDF
     */
    private function generateOccupancyPdf($data, $request)
    {
        $html = view('admin.reports.occupancy-pdf', compact('data', 'request'))->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="occupancy_report.html"');
    }

    /**
     * Generate revenue CSV
     */
    private function generateRevenueCsv($data, $request)
    {
        $filename = 'revenue_report_' . $request->group_by . '_' . $request->date_from . '_to_' . $request->date_to . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $request) {
            $file = fopen('php://output', 'w');

            if ($request->group_by === 'room_category') {
                fputcsv($file, ['Room Category', 'Total Bookings', 'Total Revenue', 'Average Revenue']);
                foreach ($data as $row) {
                    fputcsv($file, [$row['category'], $row['total_bookings'], $row['total_revenue'], $row['average_revenue']]);
                }
            } else {
                $period = $request->group_by === 'daily' ? 'Date' : 'Month';
                fputcsv($file, [$period, 'Total Bookings', 'Total Revenue']);
                foreach ($data as $row) {
                    $key = $request->group_by === 'daily' ? 'date' : 'month';
                    fputcsv($file, [$row[$key], $row['total_bookings'], $row['total_revenue']]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate revenue PDF
     */
    private function generateRevenuePdf($data, $request)
    {
        $html = view('admin.reports.revenue-pdf', compact('data', 'request'))->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="revenue_report.html"');
    }
}
