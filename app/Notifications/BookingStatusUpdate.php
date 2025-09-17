<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, string $oldStatus, string $newStatus)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;
        $statusMessages = [
            'confirmed' => 'Your booking has been confirmed!',
            'checked_in' => 'Welcome! You have been checked in.',
            'checked_out' => 'Thank you for staying with us! You have been checked out.',
        ];

        $message = (new MailMessage)
            ->subject('Booking Status Update - ' . $booking->booking_reference)
            ->greeting('Hello ' . $booking->customer->user->name . '!')
            ->line($statusMessages[$this->newStatus] ?? 'Your booking status has been updated.')
            ->line('Booking Details:')
            ->line('Reference: ' . $booking->booking_reference)
            ->line('Room: ' . $booking->room->room_number . ' (' . $booking->room->roomCategory->name . ')')
            ->line('Check-in: ' . $booking->check_in_date->format('M d, Y'))
            ->line('Check-out: ' . $booking->check_out_date->format('M d, Y'))
            ->line('Status: ' . ucfirst(str_replace('_', ' ', $this->newStatus)));

        if ($this->newStatus === 'checked_in') {
            $message->line('We hope you enjoy your stay! If you need anything, please don\'t hesitate to contact our front desk.');
        } elseif ($this->newStatus === 'checked_out') {
            $message->line('We hope you had a wonderful stay! Please consider leaving us a review.')
                    ->action('Leave Review', route('rooms.index'));
        } else {
            $message->action('View Booking Details', route('bookings.show', $booking));
        }

        $message->line('Thank you for choosing Hotello!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => 'Your booking status has been updated.',
        ];
    }
}
