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

    public function __construct(
        public Appointment $appointment,
        public string $type = 'requested'
    ) {
    }

    public function envelope(): Envelope
    {
        $customerName = trim(implode(' ', array_filter([
            $this->appointment->customer?->first_name,
            $this->appointment->customer?->last_name,
        ])));

        $subjects = [
            'requested' => 'Neue Terminanfrage',
            'confirmed' => 'Termin wurde bestaetigt',
            'rejected' => 'Terminanfrage wurde abgelehnt',
            'cancelled' => 'Termin wurde storniert',
            'rescheduled' => 'Termin wurde verschoben',
            'updated' => 'Termin wurde aktualisiert',
        ];

        $subject = $subjects[$this->type] ?? 'Termin wurde aktualisiert';

        return new Envelope(
            subject: $customerName !== '' ? $subject.' - '.$customerName : $subject
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
