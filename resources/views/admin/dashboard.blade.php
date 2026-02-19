<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 text-base-content">
    <header class="navbar bg-base-100 shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex w-full items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">Admin Dashboard</div>
                    <div class="text-sm text-base-content/60">{{ Auth::user()->name }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 space-y-6">
        @if (session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="card bg-base-100 shadow-md">
            <div class="card-body">
                <h2 class="card-title">Requested Appointments</h2>

                @if ($requestedAppointments->isEmpty())
                    <p class="text-sm text-base-content/70">Keine offenen Anfragen.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Zeit</th>
                                    <th>Kunde</th>
                                    <th>Service</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requestedAppointments as $appointment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
                                        <td>{{ $appointment->customer?->first_name }} {{ $appointment->customer?->last_name }}</td>
                                        <td>{{ $appointment->service?->name }}</td>
                                        <td class="flex flex-wrap gap-2">
                                            <form method="POST" action="{{ route('admin.appointments.accept', $appointment) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-success">Accept</button>
                                            </form>
                                            <a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-xs btn-outline">Bearbeiten</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        <section class="card bg-base-100 shadow-md">
            <div class="card-body">
                <h2 class="card-title">Confirmed Appointments (neueste 50)</h2>

                @if ($confirmedAppointments->isEmpty())
                    <p class="text-sm text-base-content/70">Keine bestaetigten Termine.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Zeit</th>
                                    <th>Kunde</th>
                                    <th>Service</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($confirmedAppointments as $appointment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
                                        <td>{{ $appointment->customer?->first_name }} {{ $appointment->customer?->last_name }}</td>
                                        <td>{{ $appointment->service?->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        <section class="card bg-base-100 shadow-md">
            <div class="card-body">
                <h2 class="card-title">Verfuegbarkeit pruefen</h2>
                <p class="text-sm text-base-content/70">Du siehst freie und bereits angefragte Slots. Bestaetigte Slots erscheinen nicht.</p>

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label for="service_id" class="label"><span class="label-text">Service</span></label>
                        <select id="service_id" class="select select-bordered w-full">
                            <option value="">Bitte waehlen</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration }} Min)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="date" class="label"><span class="label-text">Datum</span></label>
                        <input id="date" type="date" class="input input-bordered w-full">
                    </div>
                </div>

                <div id="slots-container" class="mt-4 grid grid-cols-2 gap-2 md:grid-cols-4"></div>
                <p id="slots-message" class="mt-3 text-sm text-base-content/70"></p>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const serviceEl = document.getElementById('service_id');
            const dateEl = document.getElementById('date');
            const slotsContainer = document.getElementById('slots-container');
            const slotsMessage = document.getElementById('slots-message');
            const availabilityUrl = "{{ url('/availability') }}";

            function renderMessage(message) {
                slotsMessage.textContent = message;
            }

            function renderSlots(slots) {
                slotsContainer.innerHTML = '';

                if (!slots || slots.length === 0) {
                    renderMessage('Keine sichtbaren Slots fuer diese Auswahl.');
                    return;
                }

                renderMessage('Status je Slot: frei oder requested.');

                slots.forEach(function (slot) {
                    const item = document.createElement('div');
                    item.className = 'btn btn-sm w-full pointer-events-none ' + (slot.status === 'requested' ? 'btn-warning' : 'btn-outline');
                    item.textContent = slot.start_time + ' - ' + slot.end_time + (slot.status === 'requested' ? ' (Requested)' : ' (Frei)');
                    slotsContainer.appendChild(item);
                });
            }

            async function loadSlots() {
                const serviceId = serviceEl.value;
                const date = dateEl.value;

                slotsContainer.innerHTML = '';

                if (!serviceId || !date) {
                    renderMessage('Bitte zuerst Service und Datum waehlen.');
                    return;
                }

                renderMessage('Lade Slots...');

                try {
                    const response = await fetch(
                        availabilityUrl + '?date=' + encodeURIComponent(date) + '&service_id=' + encodeURIComponent(serviceId),
                        { headers: { 'Accept': 'application/json' } }
                    );

                    if (!response.ok) {
                        renderMessage('Slots konnten nicht geladen werden.');
                        return;
                    }

                    const data = await response.json();
                    renderSlots(data.slots);
                } catch (error) {
                    renderMessage('Netzwerkfehler beim Laden.');
                }
            }

            serviceEl.addEventListener('change', loadSlots);
            dateEl.addEventListener('change', loadSlots);
            renderMessage('Bitte zuerst Service und Datum waehlen.');
        });
    </script>
</body>
</html>

