<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Termin bearbeiten</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 text-base-content">
    <main class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-2xl card bg-base-100 shadow-md">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <h1 class="card-title">Termin bearbeiten</h1>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline">Zurueck</a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-error mt-2">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="rounded-box bg-base-200 p-3 text-sm">
                    <div><strong>Kunde:</strong> {{ $appointment->customer?->first_name }} {{ $appointment->customer?->last_name }}</div>
                    <div><strong>Kontakt:</strong> {{ $appointment->customer?->email ?? '-' }} / {{ $appointment->customer?->phone ?? '-' }}</div>
                </div>

                <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="label" for="service_id"><span class="label-text">Service</span></label>
                        <select id="service_id" name="service_id" class="select select-bordered w-full" required>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" @selected(old('service_id', $appointment->service_id) == $service->id)>
                                    {{ $service->name }} ({{ $service->duration }} Min)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="label" for="date"><span class="label-text">Datum</span></label>
                            <input id="date" name="date" type="date" class="input input-bordered w-full" value="{{ old('date', \Carbon\Carbon::parse($appointment->date)->format('Y-m-d')) }}" required>
                        </div>
                        <div>
                            <label class="label" for="start_time"><span class="label-text">Start</span></label>
                            <input id="start_time" name="start_time" type="time" class="input input-bordered w-full" value="{{ old('start_time', \Carbon\Carbon::parse($appointment->start_time)->format('H:i')) }}" required>
                        </div>
                        <div>
                            <label class="label" for="end_time"><span class="label-text">Ende</span></label>
                            <input id="end_time" name="end_time" type="time" class="input input-bordered w-full" value="{{ old('end_time', \Carbon\Carbon::parse($appointment->end_time)->format('H:i')) }}" required>
                        </div>
                    </div>

                    <div>
                        <label class="label" for="status"><span class="label-text">Status</span></label>
                        <select id="status" name="status" class="select select-bordered w-full" required>
                            @foreach (['requested', 'confirmed', 'cancelled', 'completed'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $appointment->status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="staff_note"><span class="label-text">Staff Note</span></label>
                        <textarea id="staff_note" name="staff_note" rows="3" class="textarea textarea-bordered w-full">{{ old('staff_note', $appointment->staff_note) }}</textarea>
                    </div>

                    <div>
                        <label class="label" for="cancel_reason"><span class="label-text">Cancel Reason (nur bei cancelled)</span></label>
                        <input id="cancel_reason" name="cancel_reason" type="text" class="input input-bordered w-full" value="{{ old('cancel_reason', $appointment->cancel_reason) }}">
                    </div>

                    <button type="submit" class="btn btn-primary w-full">Speichern</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>

