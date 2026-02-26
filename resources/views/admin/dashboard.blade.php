<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="barber-theme min-h-screen">
    {{-- Adminbereich nutzt dieselbe Design-Sprache wie Welcome/Login/Register. --}}
    <div class="barber-page-shell">
        <div class="barber-page-content">
            {{-- Obere Navbar mit Logo links und Aktionen rechts. --}}
            <header class="barber-navbar-shell">
                <div class="barber-navbar barber-panel">
                    <a href="{{ route('dashboard') }}" class="barber-navbar-brand">
                        <div class="barber-logo-frame barber-logo-frame--nav">
                            <img src="{{ asset('images/barber-logo.jpg') }}" alt="Barber Logo">
                        </div>
                        <div class="barber-navbar-brand-copy">
                            <div class="barber-navbar-title">Admin Dashboard</div>
                            <div class="barber-navbar-subtitle">{{ Auth::user()->name }}</div>
                        </div>
                    </a>

                    <div class="barber-navbar-actions">
                        <a href="{{ route('welcome') }}" class="btn btn-sm btn-outline">Buchungsseite</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 md:px-6 md:py-8">
                <div class="barber-stack">
                    {{-- Uebersichtskarten geben einen schnellen Status vor den Tabellen. --}}
                    <section class="barber-panel p-5 md:p-6">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <span class="barber-badge">Terminverwaltung</span>
                                <h1 class="barber-heading mt-3 text-3xl font-semibold">Admin Uebersicht</h1>
                                <p class="mt-2 text-sm text-[#c9bfb3]">
                                    Offene Anfragen bearbeiten, bestaetigte Termine pruefen und Verfuegbarkeiten kontrollieren.
                                </p>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div class="barber-panel-muted p-3 text-center">
                                    <div class="text-xs uppercase tracking-[0.16em] text-[#c9bfb3]">Offen</div>
                                    <div class="mt-1 text-2xl font-semibold text-[#f0c08f]">{{ $requestedAppointments->count() }}</div>
                                </div>
                                <div class="barber-panel-muted p-3 text-center">
                                    <div class="text-xs uppercase tracking-[0.16em] text-[#c9bfb3]">Bestaetigt</div>
                                    <div class="mt-1 text-2xl font-semibold text-[#f0c08f]">{{ $confirmedAppointments->count() }}</div>
                                </div>
                                <div class="barber-panel-muted p-3 text-center">
                                    <div class="text-xs uppercase tracking-[0.16em] text-[#c9bfb3]">Services</div>
                                    <div class="mt-1 text-2xl font-semibold text-[#f0c08f]">{{ $services->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Meldungen stehen prominent ueber den Arbeitsbereichen. --}}
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

                    {{-- Haupttabelle fuer offene Terminanfragen. --}}
                    <section class="barber-panel p-5 md:p-6">
                        <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="barber-heading text-2xl font-semibold">Offene Anfragen</h2>
                                <p class="mt-1 text-sm text-[#c9bfb3]">Direkt akzeptieren, ablehnen oder in die Detailbearbeitung wechseln.</p>
                            </div>
                            <span class="barber-chip">{{ $requestedAppointments->count() }} Eintraege</span>
                        </div>

                        @if ($requestedAppointments->isEmpty())
                            <p class="text-sm text-[#c9bfb3]">Keine offenen Anfragen.</p>
                        @else
                            <div class="barber-table-wrap overflow-x-auto">
                                <table class="barber-table">
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
                                                <td>
                                                    {{-- Aktionen bleiben bei bestehenden Routen/Controllern. --}}
                                                    <div class="barber-actions-inline">
                                                        <form method="POST" action="{{ route('admin.appointments.accept', $appointment) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-xs btn-success">Accept</button>
                                                        </form>
                                                        {{-- Ablehnen storniert die offene Anfrage direkt aus der Uebersicht. --}}
                                                        <form method="POST" action="{{ route('admin.appointments.reject', $appointment) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-xs btn-warning">Ablehnen</button>
                                                        </form>
                                                        <a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-xs btn-outline">Bearbeiten</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </section>

                    {{-- Bestaetigte Termine als zweite Tabelle fuer schnellen Verlauf. --}}
                    <section class="barber-panel p-5 md:p-6">
                        <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="barber-heading text-2xl font-semibold">Bestaetigte Termine (neueste 50)</h2>
                                <p class="mt-1 text-sm text-[#c9bfb3]">Nur Leseansicht fuer schnellen Ueberblick.</p>
                            </div>
                            <span class="barber-chip">{{ $confirmedAppointments->count() }} sichtbar</span>
                        </div>

                        @if ($confirmedAppointments->isEmpty())
                            <p class="text-sm text-[#c9bfb3]">Keine bestaetigten Termine.</p>
                        @else
                            <div class="barber-table-wrap overflow-x-auto">
                                <table class="barber-table">
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
                                        @foreach ($confirmedAppointments as $appointment)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
                                                <td>{{ $appointment->customer?->first_name }} {{ $appointment->customer?->last_name }}</td>
                                                <td>{{ $appointment->service?->name }}</td>
                                                <td>
                                                    {{-- Auch bestaetigte Termine koennen bearbeitet oder storniert werden. --}}
                                                    <div class="barber-actions-inline">
                                                        <a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-xs btn-outline">Bearbeiten</a>
                                                        <form method="POST" action="{{ route('admin.appointments.cancel', $appointment) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-xs btn-warning">Stornieren</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </section>

                    {{-- Verfuegbarkeits-Check bleibt technisch gleich, ist aber optisch neu gruppiert. --}}
                    <section class="barber-panel p-5 md:p-6">
                        <div class="mb-4">
                            <h2 class="barber-heading text-2xl font-semibold">Verfuegbarkeit pruefen</h2>
                            <p class="mt-2 text-sm text-[#c9bfb3]">
                                Freie und angefragte Slots werden angezeigt. Bestaetigte Slots bleiben ausgeblendet.
                            </p>
                        </div>

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

                        {{-- Slotstatus wird per JavaScript in diesen Container geschrieben. --}}
                        <div id="slots-container" class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-4"></div>
                        <p id="slots-message" class="mt-3 text-sm text-[#c9bfb3]"></p>
                    </section>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const serviceEl = document.getElementById('service_id');
            const dateEl = document.getElementById('date');
            const slotsContainer = document.getElementById('slots-container');
            const slotsMessage = document.getElementById('slots-message');
            const availabilityUrl = "{{ url('/availability') }}";

            // Schreibt Hinweistext unter den Admin-Slotbereich.
            function renderMessage(message) {
                slotsMessage.textContent = message;
            }

            // Zeigt Slots als nicht klickbare Status-Elemente (Frei / Requested).
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

            // Laedt die Verfuegbarkeiten ueber den bestehenden Endpoint.
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
