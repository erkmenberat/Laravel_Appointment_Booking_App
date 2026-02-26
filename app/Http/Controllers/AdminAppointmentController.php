<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminAppointmentController extends Controller
{
    public function index(): View
    {
        $requestedAppointments = Appointment::query()
            ->with(['customer', 'service'])
            ->where('status', 'requested')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $confirmedAppointments = Appointment::query()
            ->with(['customer', 'service'])
            ->where('status', 'confirmed')
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(50)
            ->get();

        $services = Service::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration']);

        return view('admin.dashboard', [
            'requestedAppointments' => $requestedAppointments,
            'confirmedAppointments' => $confirmedAppointments,
            'services' => $services,
        ]);
    }

    public function edit(Appointment $appointment): View
    {
        $services = Service::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration']);

        return view('admin.appointments.edit', [
            'appointment' => $appointment->load(['customer', 'service']),
            'services' => $services,
        ]);
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['required', 'in:requested,confirmed,cancelled,completed'],
            'staff_note' => ['nullable', 'string'],
            'cancel_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $service = Service::findOrFail((int) $validated['service_id']);

        if (!$this->isSlotInsideBusinessHours(
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
            (int) $service->duration
        )) {
            return back()
                ->withErrors(['start_time' => 'Zeitfenster passt nicht zu Oeffnungszeiten oder Servicedauer.'])
                ->withInput();
        }

        try {
            DB::transaction(function () use ($appointment, $validated) {
                $freshAppointment = Appointment::query()
                    ->whereKey($appointment->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $targetStatus = $validated['status'];
                $shouldBlockSlot = in_array($targetStatus, ['requested', 'confirmed'], true);

                if ($shouldBlockSlot) {
                    $hasConflict = Appointment::query()
                        ->whereDate('date', $validated['date'])
                        ->where('id', '!=', $freshAppointment->id)
                        ->whereIn('status', ['requested', 'confirmed'])
                        ->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>', $validated['start_time'])
                        ->lockForUpdate()
                        ->exists();

                    if ($hasConflict) {
                        throw new \RuntimeException('slot_conflict');
                    }
                }

                $freshAppointment->fill([
                    'service_id' => (int) $validated['service_id'],
                    'date' => $validated['date'],
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'status' => $targetStatus,
                    'staff_note' => $validated['staff_note'] ?? null,
                    'cancel_reason' => $targetStatus === 'cancelled'
                        ? ($validated['cancel_reason'] ?? 'Vom Admin storniert.')
                        : null,
                    'cancelled_at' => $targetStatus === 'cancelled' ? now() : null,
                ]);
                $freshAppointment->save();
            });
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() === 'slot_conflict') {
                return back()
                    ->withErrors(['start_time' => 'Dieses Zeitfenster ist bereits angefragt oder bestaetigt.'])
                    ->withInput();
            }

            throw $exception;
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Termin wurde aktualisiert.');
    }

    public function accept(Appointment $appointment): RedirectResponse
    {
        try {
            DB::transaction(function () use ($appointment) {
                $target = Appointment::query()
                    ->whereKey($appointment->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($target->status === 'cancelled' || $target->status === 'completed') {
                    throw new \RuntimeException('invalid_status');
                }

                $hasConfirmedConflict = Appointment::query()
                    ->whereDate('date', $target->date)
                    ->where('id', '!=', $target->id)
                    ->where('status', 'confirmed')
                    ->where('start_time', '<', $target->end_time)
                    ->where('end_time', '>', $target->start_time)
                    ->lockForUpdate()
                    ->exists();

                if ($hasConfirmedConflict) {
                    throw new \RuntimeException('confirmed_conflict');
                }

                $target->status = 'confirmed';
                $target->cancel_reason = null;
                $target->cancelled_at = null;
                $target->save();

                Appointment::query()
                    ->whereDate('date', $target->date)
                    ->where('id', '!=', $target->id)
                    ->where('status', 'requested')
                    ->where('start_time', '<', $target->end_time)
                    ->where('end_time', '>', $target->start_time)
                    ->update([
                        'status' => 'cancelled',
                        'cancel_reason' => 'Zeitfenster bereits bestaetigt.',
                        'cancelled_at' => now(),
                    ]);
            });
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() === 'invalid_status') {
                return back()->withErrors(['accept' => 'Dieser Termin kann nicht mehr bestaetigt werden.']);
            }

            if ($exception->getMessage() === 'confirmed_conflict') {
                return back()->withErrors(['accept' => 'Es gibt bereits einen bestaetigten Termin in diesem Zeitfenster.']);
            }

            throw $exception;
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Termin wurde bestaetigt.');
    }

    public function reject(Appointment $appointment): RedirectResponse
    {
        try {
            DB::transaction(function () use ($appointment) {
                $target = Appointment::query()
                    ->whereKey($appointment->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Ablehnen ist nur fuer offene Anfragen gedacht, damit die Aktion eindeutig bleibt.
                if ($target->status !== 'requested') {
                    throw new \RuntimeException('invalid_status');
                }

                $target->status = 'cancelled';
                $target->cancel_reason = 'Vom Admin abgelehnt.';
                $target->cancelled_at = now();
                $target->save();
            });
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() === 'invalid_status') {
                return back()->withErrors(['reject' => 'Nur angefragte Termine koennen abgelehnt werden.']);
            }

            throw $exception;
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Anfrage wurde abgelehnt.');
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        try {
            DB::transaction(function () use ($appointment) {
                $target = Appointment::query()
                    ->whereKey($appointment->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Stornieren soll fuer bestaetigte (und optional angefragte) Termine moeglich sein,
                // aber nicht erneut auf bereits abgeschlossene/stornierte Termine angewendet werden.
                if (in_array($target->status, ['cancelled', 'completed'], true)) {
                    throw new \RuntimeException('invalid_status');
                }

                $wasConfirmed = $target->status === 'confirmed';
                $target->status = 'cancelled';
                $target->cancel_reason = $wasConfirmed
                    ? 'Vom Admin storniert (bestaetigter Termin).'
                    : 'Vom Admin storniert.';
                $target->cancelled_at = now();
                $target->save();
            });
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() === 'invalid_status') {
                return back()->withErrors(['cancel' => 'Dieser Termin kann nicht storniert werden.']);
            }

            throw $exception;
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Termin wurde storniert.');
    }

    private function isSlotInsideBusinessHours(
        string $date,
        string $startTime,
        string $endTime,
        int $serviceDuration
    ): bool {
        if ($serviceDuration <= 0) {
            return false;
        }

        $day = Carbon::parse($date);
        $weekday = $day->isoWeekday();
        $businessHour = BusinessHour::query()->where('weekday', $weekday)->first();

        if (!$businessHour || $businessHour->is_closed || !$businessHour->open_time || !$businessHour->close_time) {
            return false;
        }

        $dayStart = Carbon::parse($date.' '.$businessHour->open_time);
        $dayEnd = Carbon::parse($date.' '.$businessHour->close_time);
        $period = CarbonPeriod::create($dayStart, $serviceDuration.' minutes', $dayEnd);

        foreach ($period as $candidateStart) {
            $candidateEnd = $candidateStart->copy()->addMinutes($serviceDuration);

            if (
                $candidateEnd->lte($dayEnd)
                && $candidateStart->format('H:i') === $startTime
                && $candidateEnd->format('H:i') === $endTime
            ) {
                return true;
            }
        }

        return false;
    }
}
