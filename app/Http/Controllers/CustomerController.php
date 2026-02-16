<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

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

        return redirect()->route('welcome')->with('success', 'Termin erfolgreich angefragt.');
    }
}
