<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
        ]);

        // 1) Service laden (wir brauchen die Dauer in Minuten)
        $service = Service::findOrFail($validated['service_id']);

        // 2) Wochentag aus Datum bestimmen (1=Montag ... 7=Sonntag)
        $date = Carbon::parse($validated['date']);
        $weekday = $date->isoWeekday();

        // 3) Öffnungszeit für diesen Tag laden
        $businessHour = BusinessHour::where('weekday', $weekday)->first();

        // Sicherheitscheck: kein Eintrag oder geschlossen => keine Slots
        if (!$businessHour || $businessHour->is_closed || !$businessHour->open_time || !$businessHour->close_time) {
            return response()->json([
                'date' => $validated['date'],
                'service_id' => (int) $validated['service_id'],
                'slots' => [],
                'reason' => 'closed_or_no_business_hours',
            ]);
        }

        // 4) Start/Ende des Arbeitstags auf das gewählte Datum setzen
        $dayStart = Carbon::parse($validated['date'].' '.$businessHour->open_time);
        $dayEnd = Carbon::parse($validated['date'].' '.$businessHour->close_time);

        $duration = (int) $service->duration;
        if ($duration <= 0) {
            return response()->json([
                'date' => $validated['date'],
                'service_id' => (int) $validated['service_id'],
                'slots' => [],
                'reason' => 'invalid_service_duration',
            ], 422);
        }

        // 5) Kein ueberlappendes Raster: Start springt immer um die Service-Dauer.
        $stepMinutes = $duration;

        $slots = [];
        $blockingStatuses = ['requested', 'confirmed'];
        $appointments = Appointment::query()
            ->whereDate('date', $validated['date'])
            ->whereIn('status', $blockingStatuses)
            ->get(['start_time', 'end_time', 'status']);
        $period = CarbonPeriod::create($dayStart, $stepMinutes.' minutes', $dayEnd);

        foreach ($period as $candidateStart) {
            $candidateEnd = $candidateStart->copy()->addMinutes($duration);

            // Slot nur zulassen, wenn Termin vollständig vor Schließzeit endet
            if ($candidateEnd->lte($dayEnd)) {
                $candidateStartTime = $candidateStart->format('H:i:s');
                $candidateEndTime = $candidateEnd->format('H:i:s');

                $overlapAppointment = $appointments->first(function ($appointment) use ($candidateStartTime, $candidateEndTime) {
                    return $appointment->start_time < $candidateEndTime
                        && $appointment->end_time > $candidateStartTime;
                });

                if ($overlapAppointment && $overlapAppointment->status === 'confirmed') {
                    continue;
                }

                $slots[] = [
                    'start_time' => $candidateStart->format('H:i'),
                    'end_time' => $candidateEnd->format('H:i'),
                    'status' => $overlapAppointment ? 'requested' : 'free',
                ];
            }
        }

        return response()->json([
            'date' => $validated['date'],
            'service_id' => (int) $validated['service_id'],
            'service_duration' => $duration,
            'weekday' => $weekday,
            'slots' => $slots,
        ]);
    }
}
