<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Notifications\BookingConfirmation;
use App\Notifications\BookingCancellation;
use App\Notifications\BookingStatusUpdate;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'customer_id',
        'room_id',
        'agent_id',
        'check_in_date',
        'check_out_date',
        'guests',
        'total_amount',
        'paid_amount',
        'status',
        'payment_status',
        'special_requests',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = 'BK' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the customer for this booking
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the room for this booking
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the agent who created this booking
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Calculate the number of nights
     */
    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    /**
     * Calculate the remaining amount to be paid
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) &&
               $this->check_in_date->isFuture();
    }

    /**
     * Cancel the booking
     */
    public function cancel(string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        // Send cancellation notification
        $this->customer->user->notify(new BookingCancellation($this));

        return true;
    }

    /**
     * Confirm the booking
     */
    public function confirm(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $oldStatus = $this->status;
        $this->update(['status' => 'confirmed']);

        // Send confirmation notification
        $this->customer->user->notify(new BookingConfirmation($this));

        return true;
    }

    /**
     * Check in the booking
     */
    public function checkIn(): bool
    {
        if ($this->status !== 'confirmed') {
            return false;
        }

        $oldStatus = $this->status;
        $this->update(['status' => 'checked_in']);

        // Send status update notification
        $this->customer->user->notify(new BookingStatusUpdate($this, $oldStatus, 'checked_in'));

        return true;
    }

    /**
     * Check out the booking
     */
    public function checkOut(): bool
    {
        if ($this->status !== 'checked_in') {
            return false;
        }

        $oldStatus = $this->status;
        $this->update(['status' => 'checked_out']);

        // Send status update notification
        $this->customer->user->notify(new BookingStatusUpdate($this, $oldStatus, 'checked_out'));

        return true;
    }

    /**
     * Scope to get bookings by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get bookings by date range
     */
    public function scopeByDateRange($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('check_in_date', [$startDate, $endDate])
                ->orWhereBetween('check_out_date', [$startDate, $endDate])
                ->orWhere(function ($subQ) use ($startDate, $endDate) {
                    $subQ->where('check_in_date', '<=', $startDate)
                        ->where('check_out_date', '>=', $endDate);
                });
        });
    }
}
