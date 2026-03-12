<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #333; padding: 2rem;">

    <h2>Neue Terminanfrage</h2>

    <p>Es wurde eine neue Terminanfrage ueber das Buchungsformular erstellt.</p>

    <table style="border-collapse: collapse; width: 100%; max-width: 560px;">
        <tr>
            <td style="padding: 0.4rem 0; color: #888; width: 180px;">Kunde:</td>
            <td>{{ trim(($appointment->customer?->first_name ?? '').' '.($appointment->customer?->last_name ?? '')) }}</td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">E-Mail:</td>
            <td>{{ $appointment->customer?->email ?: '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Telefon:</td>
            <td>{{ $appointment->customer?->phone ?: '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Service:</td>
            <td>{{ $appointment->service?->name ?: '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Datum:</td>
            <td>{{ $appointment->date?->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Zeit:</td>
            <td>
                {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}
                -
                {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }} Uhr
            </td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #888; vertical-align: top;">Notiz:</td>
            <td>{{ $appointment->customer_note ?: '-' }}</td>
        </tr>
    </table>

    <p style="margin-top: 2rem;">
        Bitte im Admin-Bereich pruefen und die Anfrage bestaetigen oder ablehnen.
    </p>

    <p>
        <a href="{{ route('login') }}" style="display: inline-block; padding: 0.75rem 1.1rem; background: #1f2937; color: #fff; text-decoration: none; border-radius: 6px;">
            Zum Admin-Login
        </a>
    </p>

</body>
</html>