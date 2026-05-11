<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $type
    ) {
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'requested' => 'Deine Terminanfrage ist eingegangen',
            'confirmed' => 'Dein Termin wurde bestaetigt',
            'rejected' => 'Deine Terminanfrage wurde abgelehnt',
            'cancelled' => 'Dein Termin wurde storniert',
            'rescheduled' => 'Dein Termin wurde verschoben',
            'updated' => 'Dein Termin wurde aktualisiert',
        ];

        return new Envelope(
            subject: $subjects[$this->type] ?? 'Aktualisierung zu deinem Termin'
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.appointment-status');
    }

    public function attachments(): array
    {
        return [];
    }
}
