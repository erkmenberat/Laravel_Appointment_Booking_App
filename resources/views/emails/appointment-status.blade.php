<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #333; padding: 2rem;">

    @php
        $titles = [
            'requested' => 'Deine Terminanfrage ist eingegangen.',
            'confirmed' => 'Dein Termin wurde bestaetigt.',
            'rejected' => 'Deine Anfrage wurde leider abgelehnt.',
            'cancelled' => 'Dein Termin wurde storniert.',
            'rescheduled' => 'Dein Termin wurde verschoben.',
            'updated' => 'Dein Termin wurde aktualisiert.',
        ];
    @endphp

    <h2>{{ $titles[$type] ?? 'Aktualisierung zu deinem Termin.' }}</h2>

    @if ($type === 'requested')
        <p>Wir pruefen deine Anfrage und melden uns mit einer Bestaetigung oder Rueckmeldung.</p>
    @endif

    <p>Hallo {{ $appointment->customer?->first_name }},</p>

    <table style="border-collapse: collapse; width: 100%; max-width: 400px;">
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Service:</td>
            <td>{{ $appointment->service?->name ?: '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Datum:</td>
            <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Zeit:</td>
            <td>
                {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}
                -
                {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }} Uhr
            </td>
        </tr>
        @if (in_array($type, ['rejected', 'cancelled'], true) && $appointment->cancel_reason)
            <tr>
                <td style="padding: 0.4rem 0; color: #888;">Grund:</td>
                <td>{{ $appointment->cancel_reason }}</td>
            </tr>
        @endif
    </table>

    <p style="margin-top: 2rem; color: #888; font-size: 0.85rem;">
        Antworte einfach auf diese E-Mail, wenn du Fragen hast.
    </p>

</body>
</html>
