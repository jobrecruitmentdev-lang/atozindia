<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeCollectorMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to the Atelier of Precision Technology — ATOZ GADGETZ',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-collector',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
