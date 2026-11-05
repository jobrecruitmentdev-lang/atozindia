<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderDispatchedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public ?Shipment $shipment;

    public function __construct(Order $order, ?Shipment $shipment = null)
    {
        $this->order = $order;
        $this->shipment = $shipment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dispatched with Care: Order #' . $this->order->order_number . ' is in Transit — ATOZ GADGETZ',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-dispatched',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
