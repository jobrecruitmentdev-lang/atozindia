<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderAuthorizationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $userName;
    public float $orderTotal;
    public int $itemsCount;
    public string $shippingSummary;

    public function __construct(string $otp, string $userName, float $orderTotal, int $itemsCount, string $shippingSummary = '')
    {
        $this->otp = $otp;
        $this->userName = $userName;
        $this->orderTotal = $orderTotal;
        $this->itemsCount = $itemsCount;
        $this->shippingSummary = $shippingSummary;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Acquisition Authorization Code: ' . $this->otp . ' — ATOZ GADGETZ',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-authorization-otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
