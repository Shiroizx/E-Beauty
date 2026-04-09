<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShippingStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $statusLabel;
    public $trackingUrl;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->statusLabel = $order->statusLabel();
        $this->trackingUrl = route('track.show', $order->order_number);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Status Pengiriman Pesanan #' . $this->order->order_number . ' Diperbarui',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shipping-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}