<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminAppointmentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
    }

    public function envelope(): Envelope
    {
        $customerName = trim(implode(' ', array_filter([
            $this->appointment->customer?->first_name,
            $this->appointment->customer?->last_name,
        ])));

        return new Envelope(
            subject: $customerName !== ''
                ? 'Neue Terminanfrage von '.$customerName
                : 'Neue Terminanfrage'
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-appointment-request');
    }

    public function attachments(): array
    {
        return [];
    }
}