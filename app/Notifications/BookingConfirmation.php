<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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

        return (new MailMessage)
            ->subject('Booking Confirmation - ' . $booking->booking_reference)
            ->greeting('Hello ' . $booking->customer->user->name . '!')
            ->line('Your booking has been confirmed.')
            ->line('Booking Details:')
            ->line('Reference: ' . $booking->booking_reference)
            ->line('Room: ' . $booking->room->room_number . ' (' . $booking->room->roomCategory->name . ')')
            ->line('Check-in: ' . $booking->check_in_date->format('M d, Y'))
            ->line('Check-out: ' . $booking->check_out_date->format('M d, Y'))
            ->line('Guests: ' . $booking->guests)
            ->line('Total Amount: $' . number_format($booking->total_amount, 2))
            ->action('View Booking Details', route('bookings.show', $booking))
            ->line('Thank you for choosing Hotello! We look forward to hosting you.');
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
            'message' => 'Your booking has been confirmed.',
        ];
    }
}
