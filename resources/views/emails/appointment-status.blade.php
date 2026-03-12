<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #333; padding: 2rem;">

    @if ($type === 'confirmed')
        <h2>✅ Dein Termin wurde bestätigt!</h2>
    @elseif ($type === 'rejected')
        <h2>❌ Deine Anfrage wurde leider abgelehnt.</h2>
    @elseif ($type === 'cancelled')
        <h2>⚠️ Dein Termin wurde storniert.</h2>
    @endif

    <p>Hallo {{ $appointment->customer?->first_name }},</p>

    <table style="border-collapse: collapse; width: 100%; max-width: 400px;">
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Service:</td>
            <td>{{ $appointment->service?->name }}</td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Datum:</td>
            <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Zeit:</td>
            <td>
                {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}
                –
                {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }} Uhr
            </td>
        </tr>
        @if ($type === 'cancelled' && $appointment->cancel_reason)
        <tr>
            <td style="padding: 0.4rem 0; color: #888;">Grund:</td>
            <td>{{ $appointment->cancel_reason }}</td>
        </tr>
        @endif
    </table>

    <p style="margin-top: 2rem; color: #888; font-size: 0.85rem;">
        Bei Fragen kontaktiere uns direkt im Salon.
        Unter frisuersalon@fake.com oder telefonisch unter 01234 567890.
    </p>

</body>
</html>