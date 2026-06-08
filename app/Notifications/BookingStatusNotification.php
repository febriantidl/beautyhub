<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    protected $booking;
    protected $status;

    public function __construct($booking, $status)
    {
        $this->booking = $booking;
        $this->status  = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $isApproved = $this->status === 'approved';

        return [
            'title'      => $isApproved
                ? '✅ Booking Disetujui!'
                : '❌ Booking Ditolak',
            'message'    => $isApproved
                ? "Booking kamu untuk {$this->booking->mua->name} telah disetujui. Tunjukkan QR code saat acara."
                : "Maaf, booking kamu untuk {$this->booking->mua->name} ditolak. " . ($this->booking->rejection_reason ?? ''),
            'booking_id' => $this->booking->id,
            'status'     => $this->status,
            'type'       => $isApproved ? 'booking_approved' : 'booking_rejected',
        ];
    }
}