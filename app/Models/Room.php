<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_category_id',
        'room_number',
        'floor',
        'description',
        'price_per_night',
        'is_available',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_night' => 'decimal:2',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the hotel that owns the room
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the room category
     */
    public function roomCategory()
    {
        return $this->belongsTo(RoomCategory::class);
    }

    /**
     * Get the bookings for this room
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the images for this room
     */
    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    /**
     * Get the primary image for this room
     */
    public function primaryImage()
    {
        return $this->hasOne(RoomImage::class)->where('is_primary', true);
    }

    /**
     * Check if room is available for given dates
     */
    public function isAvailableForDates(Carbon $checkIn, Carbon $checkOut): bool
    {
        if (!$this->is_available || !$this->is_active) {
            return false;
        }

        $conflictingBookings = $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut->copy()->subDay()])
                    ->orWhereBetween('check_out_date', [$checkIn->copy()->addDay(), $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                    });
            })
            ->exists();

        return !$conflictingBookings;
    }

    /**
     * Scope to get only active rooms
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only available rooms
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
