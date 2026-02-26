<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'required_without:phone'],
            'phone' => ['nullable', 'string', 'max:30', 'required_without:email'],
            'note' => 'nullable|string',
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_confirmed' => ['required', 'in:1'],
        ]);

        $service = Service::findOrFail((int) $validatedData['service_id']);
        $date = Carbon::parse($validatedData['date']);
        $weekday = $date->isoWeekday();
        $businessHour = BusinessHour::where('weekday', $weekday)->first();

        if (!$businessHour || $businessHour->is_closed || !$businessHour->open_time || !$businessHour->close_time) {
            return back()
                ->withErrors(['date' => 'Fuer dieses Datum sind keine Oeffnungszeiten hinterlegt.'])
                ->withInput();
        }

        // Der gewaehlte Slot muss aus dem aktuell berechneten Verfuegbarkeitsraster stammen.
        $duration = (int) $service->duration;
        $dayStart = Carbon::parse($validatedData['date'].' '.$businessHour->open_time);
        $dayEnd = Carbon::parse($validatedData['date'].' '.$businessHour->close_time);
        $period = CarbonPeriod::create($dayStart, $duration.' minutes', $dayEnd);

        $slotIsValid = false;
        foreach ($period as $candidateStart) {
            $candidateEnd = $candidateStart->copy()->addMinutes($duration);
            if (
                $candidateEnd->lte($dayEnd)
                && $candidateStart->format('H:i') === $validatedData['start_time']
                && $candidateEnd->format('H:i') === $validatedData['end_time']
            ) {
                $slotIsValid = true;
                break;
            }
        }

        if (!$slotIsValid) {
            return back()
                ->withErrors(['start_time' => 'Bitte ein gueltiges Zeitfenster auswaehlen.'])
                ->withInput();
        }

        $requestedStart = Carbon::parse($validatedData['date'].' '.$validatedData['start_time']);
        if ($requestedStart->lt(now())) {
            return back()
                ->withErrors(['start_time' => 'Vergangene Zeitfenster koennen nicht mehr angefragt werden.'])
                ->withInput();
        }

        $customer = Customer::query()
            ->when(
                !empty($validatedData['email']),
                fn ($query) => $query->where('email', $validatedData['email']),
                fn ($query) => $query->where('phone', $validatedData['phone'])
            )
            ->first();

        if ($customer) {
            $customer->fill([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'] ?? $customer->email,
                'phone' => $validatedData['phone'] ?? $customer->phone,
                'note' => $validatedData['note'] ?? $customer->note,
            ]);
            $customer->save();
        } else {
            $customer = Customer::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'] ?? null,
                'phone' => $validatedData['phone'] ?? null,
                'note' => $validatedData['note'] ?? null,
            ]);
        }

        try {
            DB::transaction(function () use ($customer, $validatedData) {
                $requestedStart = Carbon::parse($validatedData['date'].' '.$validatedData['start_time']);
                if ($requestedStart->lt(now())) {
                    throw new \RuntimeException('slot_in_past');
                }

                $hasConflict = Appointment::query()
                    ->whereDate('date', $validatedData['date'])
                    ->whereIn('status', ['requested', 'confirmed'])
                    ->where('start_time', '<', $validatedData['end_time'])
                    ->where('end_time', '>', $validatedData['start_time'])
                    ->lockForUpdate()
                    ->exists();

                if ($hasConflict) {
                    throw new \RuntimeException('slot_conflict');
                }

                Appointment::create([
                    'customer_id' => $customer->id,
                    'service_id' => (int) $validatedData['service_id'],
                    'staff_id' => null,
                    'date' => $validatedData['date'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'status' => 'requested',
                    'customer_note' => $validatedData['note'] ?? null,
                ]);
            });
        } catch (\RuntimeException $exception) {
            if ($exception->getMessage() === 'slot_conflict') {
                return back()
                    ->withErrors(['start_time' => 'Dieses Zeitfenster wurde soeben angefragt oder bestaetigt. Bitte waehlen Sie ein anderes.'])
                    ->withInput();
            }

            if ($exception->getMessage() === 'slot_in_past') {
                return back()
                    ->withErrors(['start_time' => 'Das Zeitfenster liegt inzwischen in der Vergangenheit. Bitte waehlen Sie ein neues.'])
                    ->withInput();
            }

            throw $exception;
        }

        return redirect()->route('welcome')->with('success', 'Termin erfolgreich angefragt.');
    }
}
