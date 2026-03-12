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
                        <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Admin registrieren</a>
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
                                <h1 class="barber-heading mt-3 text-3xl font-semibold">Admin Übersicht</h1>
                                <p class="mt-2 text-sm text-[#c9bfb3]">
                                    Offene Anfragen bearbeiten, bestätigte Termine prüfen und Verfügbarkeiten kontrollieren.
                                </p>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div class="barber-panel-muted p-3 text-center">
                                    <div class="text-xs uppercase tracking-[0.16em] text-[#c9bfb3]">Offen</div>
                                    <div class="mt-1 text-2xl font-semibold text-[#f0c08f]">{{ $requestedAppointments->count() }}</div>
                                </div>
                                <div class="barber-panel-muted p-3 text-center">
                                    <div class="text-xs uppercase tracking-[0.16em] text-[#c9bfb3]">Bestätigt</div>
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

                    @if (session('warning'))
                        <div class="alert alert-warning">
                            <span>{{ session('warning') }}</span>
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
                                                <td data-label="Datum">{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
                                                <td data-label="Zeit">{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
                                                <td data-label="Kunde">{{ $appointment->customer?->first_name }} {{ $appointment->customer?->last_name }}</td>
                                                <td data-label="Service">{{ $appointment->service?->name }}</td>
                                                <td data-label="Aktionen">
                                                    {{-- Aktionen bleiben bei bestehenden Routen/Controllern. --}}
                                                    <div class="barber-actions-inline">
                                                        <form method="POST" action="{{ route('admin.appointments.accept', $appointment) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-xs btn-success">Akzeptieren</button>
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

                    {{-- Kommende bestaetigte Termine bleiben separat von vergangenen Terminen. --}}
                    <section class="barber-panel p-5 md:p-6">
                        <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="barber-heading text-2xl font-semibold">Bestätigte Termine (kommend / aktiv)</h2>
                                <p class="mt-1 text-sm text-[#c9bfb3]">Vergangene bestätigte Termine werden automatisch nach „Frühere Termine“ verschoben.</p>
                            </div>
                            <span class="barber-chip">{{ $confirmedAppointments->count() }} sichtbar</span>
                        </div>

                        @if ($confirmedAppointments->isEmpty())
                            <p class="text-sm text-[#c9bfb3]">Keine bestätigten Termine.</p>
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
                                                <td data-label="Datum">{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
                                                <td data-label="Zeit">{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
                                                <td data-label="Kunde">{{ $appointment->customer?->first_name }} {{ $appointment->customer?->last_name }}</td>
                                                <td data-label="Service">{{ $appointment->service?->name }}</td>
                                                <td data-label="Aktionen">
                                                    {{-- Auch bestaetigte Termine koennen bearbeitet oder storniert werden. --}}
                                                    <div class="barber-actions-inline">
                                                        <a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-xs btn-outline">Bearbeiten</a>
                                                        <button type="button"
                                                            class="btn btn-xs btn-primary btn-reschedule"
                                                            data-id="{{ $appointment->id }}"
                                                            data-date="{{ $appointment->date }}"
                                                            data-start="{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}"
                                                            data-end="{{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}"
                                                            data-action="{{ route('admin.appointments.reschedule', $appointment) }}">
                                                            Verschieben
                                                        </button>
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

                    {{-- Abgelaufene bestaetigte Termine werden als completed in einer eigenen Historie gezeigt. --}}
                    <section class="barber-panel p-5 md:p-6">
                        <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="barber-heading text-2xl font-semibold">Frühere Termine (neueste 50)</h2>
                                <p class="mt-1 text-sm text-[#c9bfb3]">Automatisch abgeschlossene, vergangene Termine.</p>
                            </div>
                            <span class="barber-chip">{{ $pastAppointments->count() }} sichtbar</span>
                        </div>

                        @if ($pastAppointments->isEmpty())
                            <p class="text-sm text-[#c9bfb3]">Keine früheren Termine.</p>
                        @else
                            <div class="barber-table-wrap overflow-x-auto">
                                <table class="barber-table">
                                    <thead>
                                        <tr>
                                            <th>Datum</th>
                                            <th>Zeit</th>
                                            <th>Kunde</th>
                                            <th>Service</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pastAppointments as $appointment)
                                            <tr>
                                                <td data-label="Datum">{{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}</td>
                                                <td data-label="Zeit">{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
                                                <td data-label="Kunde">{{ $appointment->customer?->first_name }} {{ $appointment->customer?->last_name }}</td>
                                                <td data-label="Service">{{ $appointment->service?->name }}</td>
                                                <td data-label="Status">Completed</td>
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
                            <h2 class="barber-heading text-2xl font-semibold">Verfügbarkeit prüfen</h2>
                            <p class="mt-2 text-sm text-[#c9bfb3]">
                                Freie und angefragte Slots werden angezeigt. Bestätigte Slots bleiben ausgeblendet.
                            </p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label for="service_id" class="label"><span class="label-text">Service</span></label>
                                <select id="service_id" class="select select-bordered w-full">
                                    <option value="">Bitte wählen</option>
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

    {{-- Modal zum Verschieben eines bestätigten Termins --}}
    <dialog id="reschedule-modal" class="barber-panel p-6 rounded-lg shadow-xl w-full max-w-md backdrop:bg-black/60">
        <h3 class="barber-heading text-xl font-semibold mb-4">Termin verschieben</h3>
        <form id="reschedule-form" method="POST">
            @csrf
            <div class="barber-stack gap-4">
                <div>
                    <label class="label"><span class="label-text">Neues Datum</span></label>
                    <input type="date" name="date" id="reschedule-date" class="input input-bordered w-full" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label"><span class="label-text">Von</span></label>
                        <input type="time" name="start_time" id="reschedule-start" class="input input-bordered w-full" required>
                    </div>
                    <div>
                        <label class="label"><span class="label-text">Bis</span></label>
                        <input type="time" name="end_time" id="reschedule-end" class="input input-bordered w-full" required>
                    </div>
                </div>
                <p class="text-xs text-[#c9bfb3]">Der Kunde erhält automatisch eine E-Mail-Benachrichtigung.</p>
                <div class="flex gap-3 justify-end pt-2">
                    <button type="button" id="reschedule-cancel" class="btn btn-sm btn-outline">Abbrechen</button>
                    <button type="submit" class="btn btn-sm btn-primary">Verschieben</button>
                </div>
            </div>
        </form>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('reschedule-modal');
            const form  = document.getElementById('reschedule-form');

            document.querySelectorAll('.btn-reschedule').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    form.action                                     = btn.dataset.action;
                    document.getElementById('reschedule-date').value  = btn.dataset.date;
                    document.getElementById('reschedule-start').value = btn.dataset.start;
                    document.getElementById('reschedule-end').value   = btn.dataset.end;
                    modal.showModal();
                });
            });

            document.getElementById('reschedule-cancel').addEventListener('click', function () {
                modal.close();
            });

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
