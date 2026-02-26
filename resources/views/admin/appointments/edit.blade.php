<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Termin bearbeiten</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="barber-theme min-h-screen">
    {{-- Same page shell as other screens for a consistent admin editing flow. --}}
    <div class="barber-page-shell">
        <div class="barber-page-content px-4 py-6 md:px-8">
            <main class="mx-auto max-w-4xl space-y-6">
                {{-- Header panel gives context and quick navigation back to dashboard. --}}
                <section class="barber-panel p-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="barber-logo-frame h-14 w-14">
                                <img src="{{ asset('images/barber-logo.jpg') }}" alt="Barber Logo">
                            </div>
                            <div>
                                <span class="barber-badge">Admin Edit</span>
                                <h1 class="barber-heading mt-2 text-2xl font-semibold">Termin bearbeiten</h1>
                            </div>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm">Zurueck</a>
                    </div>
                </section>

                {{-- Customer summary panel helps the admin see context before editing times/status. --}}
                <section class="barber-panel p-5">
                    <h2 class="barber-heading text-xl font-semibold">Kundeninformationen</h2>
                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div class="barber-panel-muted p-4 text-sm">
                            <div class="text-[#c9bfb3]">Kunde</div>
                            <div class="mt-1 font-semibold text-[#f5efe7]">
                                {{ $appointment->customer?->first_name }} {{ $appointment->customer?->last_name }}
                            </div>
                        </div>
                        <div class="barber-panel-muted p-4 text-sm">
                            <div class="text-[#c9bfb3]">Kontakt</div>
                            <div class="mt-1 font-semibold text-[#f5efe7]">
                                {{ $appointment->customer?->email ?? '-' }} / {{ $appointment->customer?->phone ?? '-' }}
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Validation errors are shown before the form fields to speed up corrections. --}}
                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Edit form keeps all original field names/routes and only updates styling + grouping. --}}
                <section class="barber-panel p-5">
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

                        {{-- Save button remains a normal form submit to preserve controller behavior. --}}
                        <button type="submit" class="btn btn-primary w-full">Speichern</button>
                    </form>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
