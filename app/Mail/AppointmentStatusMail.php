<?php

namespace App\Mail;

use App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;

class AppointmentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Appointment $appointment,
        public string $type // 'confirmed', 'rejected', 'cancelled', 'rescheduled'
    )
    {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'confirmed' => 'Dein Termin wurde bestätigt',
            'rejected'  => 'Deine Terminanfrage wurde abgelehnt',
            'cancelled' => 'Dein Termin wurde Storniert',
            'rescheduled' => 'Dein Termin wurde verschoben',
        ];

        return new Envelope(subject: $subjects[$this->type] ?? 'Aktualisierung zu deinem Termin');  
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(view: 'emails.appointment-status');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
