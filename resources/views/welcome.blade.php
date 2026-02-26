<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Terminplattform') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="barber-theme min-h-screen">
    {{-- Globaler Bildhintergrund bleibt erhalten, aber das Layout ist jetzt klarer strukturiert. --}}
    <div class="barber-page-shell">
        <div class="barber-page-content">
            {{-- Haupt-Navigation mit sauber platzierter Marke (Logo + Text) und Admin-CTA. --}}
            <header class="barber-navbar-shell">
                <div class="barber-navbar barber-panel">
                    <a href="{{ route('welcome') }}" class="barber-navbar-brand">
                        <div class="barber-logo-frame barber-logo-frame--nav">
                            <img src="{{ asset('images/barber-logo.jpg') }}" alt="Barber Logo">
                        </div>
                        <div class="barber-navbar-brand-copy">
                            <div class="barber-navbar-title">Friseursalon</div>
                            <div class="barber-navbar-subtitle">Terminbuchung</div>
                        </div>
                    </a>

                    <div class="barber-navbar-actions">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm md:btn-md">Admin Login</a>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 md:px-6 md:py-8">
                <div class="barber-stack">
                    {{-- Hero-Bereich: Branding links, Salonfoto rechts. --}}
                    <section class="barber-panel p-5 md:p-6">
                        <div class="barber-hero-grid">
                            <div class="flex flex-col justify-center">
                                <div class="mb-3 flex flex-wrap gap-2">
                                    <span class="barber-badge">Online Booking</span>
                                    <span class="barber-chip">Salon Look</span>
                                    <span class="barber-chip">Kupfer / Anthrazit</span>
                                </div>

                                <h1 class="barber-heading text-3xl font-semibold leading-tight md:text-4xl">
                                    Termine anfragen, passend zum Stil eures Salons
                                </h1>
                                <p class="mt-3 text-sm leading-6 text-[#c9bfb3] md:text-base">
                                    Das Farbschema orientiert sich am bereitgestellten Logo und dem Salonfoto.
                                    Die Seite ist jetzt kompakter aufgebaut: oben Branding, darunter direkt das Formular.
                                </p>

                                {{-- Kleine Info-Chips geben dem Hero mehr Struktur, ohne zu ueberladen. --}}
                                <div class="mt-5 flex flex-wrap gap-2">
                                    <span class="barber-chip">Schnelle Anfrage</span>
                                    <span class="barber-chip">Slot-Auswahl live</span>
                                    <span class="barber-chip">Mobil nutzbar</span>
                                </div>
                            </div>

                            <div class="barber-panel-muted barber-photo-card">
                                <img src="{{ asset('images/salon-design.jpg') }}" alt="Salon Interior">
                                <div class="barber-photo-overlay">
                                    <div class="text-xs uppercase tracking-[0.2em] text-[#f0c08f]">Salon Atmosphaere</div>
                                    <div class="text-sm text-[#e6d8c6]">Warm beleuchtetes Interior als visuelle Vorlage für das UI.</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Unterer Bereich: Formular links (Fokus), Infos rechts (Unterstuetzung). --}}
                    <section class="barber-two-col">
                        <div class="barber-panel p-5 md:p-6">
                            <div class="mb-4">
                                <h2 class="barber-heading text-2xl font-semibold">Terminformular</h2>
                                <p class="mt-2 text-sm text-[#c9bfb3]">
                                    Bitte zuerst Service und Datum wählen. Danach werden passende Zeitfenster geladen.
                                </p>
                            </div>

                            {{-- Erfolgsmeldung nach dem Absenden der Anfrage. --}}
                            @if (session('success'))
                                <div class="alert alert-success mb-4">
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            {{-- Validierungsfehler direkt ueber dem Formular anzeigen. --}}
                            @if ($errors->any())
                                <div class="alert alert-error mb-4">
                                    <ul class="list-disc pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('customer.store') }}" method="POST" id="booking-form" class="space-y-4" autocomplete="off">
                                @csrf

                                <div>
                                    <label for="service_id" class="label"><span class="label-text">Service</span></label>
                                    <select name="service_id" id="service_id" class="select select-bordered w-full" required>
                                        <option value="">Bitte wählen</option>
                                        @foreach (\App\Models\Service::where('is_active', true)->orderBy('name')->get() as $service)
                                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }} ({{ $service->duration }} Min)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="date" class="label"><span class="label-text">Datum</span></label>
                                    <input type="date" id="date" name="date" required class="input input-bordered w-full">
                                </div>

                                {{-- Slot-Block wird durch JavaScript dynamisch mit Buttons gefuellt. --}}
                                <div class="barber-panel-muted p-4">
                                    <div class="mb-3 font-semibold text-[#f0c08f]">Verfügbare Zeitfenster</div>
                                    <div id="slots-container" class="grid grid-cols-1 gap-2 sm:grid-cols-2"></div>
                                    <p id="slots-message" class="mt-3 text-sm text-[#c9bfb3]"></p>
                                    <p id="slot-selection-message" class="mt-2 text-sm text-[#f8a9a9]"></p>
                                </div>

                                {{-- Hidden Inputs speichern den aktiv gewaehlten Slot fuer den Submit. --}}
                                <input type="hidden" name="start_time" id="start_time" value="">
                                <input type="hidden" name="end_time" id="end_time" value="">
                                <input type="hidden" name="slot_confirmed" id="slot_confirmed" value="">

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="first_name" class="label"><span class="label-text">Vorname</span></label>
                                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" class="input input-bordered w-full">
                                    </div>
                                    <div>
                                        <label for="last_name" class="label"><span class="label-text">Nachname</span></label>
                                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" class="input input-bordered w-full">
                                    </div>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="phone" class="label"><span class="label-text">Telefon</span></label>
                                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="input input-bordered w-full">
                                    </div>
                                    <div>
                                        <label for="email" class="label"><span class="label-text">E-Mail</span></label>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full">
                                    </div>
                                </div>

                                <div>
                                    <label for="note" class="label"><span class="label-text">Notiz (optional)</span></label>
                                    <textarea id="note" name="note" rows="3" class="textarea textarea-bordered w-full">{{ old('note') }}</textarea>
                                </div>

                                {{-- Hauptaktion sendet die Anfrage erst nach erfolgreicher Slot-Auswahl. --}}
                                <button type="submit" class="btn btn-primary w-full">Termin anfragen</button>
                            </form>
                        </div>

                        <div class="flex flex-col gap-4">
                            {{-- Rechte Spalte liefert Erklaerungen und schafft visuelles Gleichgewicht zum Formular. --}}
                            <section class="barber-panel p-5">
                                <h2 class="barber-heading text-xl font-semibold">So funktioniert es</h2>
                                <ol class="mt-4 space-y-3 text-sm text-[#c9bfb3]">
                                    <li><span class="text-[#f0c08f]">1.</span> Service auswaehlen</li>
                                    <li><span class="text-[#f0c08f]">2.</span> Datum auswaehlen</li>
                                    <li><span class="text-[#f0c08f]">3.</span> Freien Slot anklicken</li>
                                    <li><span class="text-[#f0c08f]">4.</span> Kontaktdaten absenden</li>
                                </ol>
                            </section>

                            <section class="barber-panel p-5">
                                <h2 class="barber-heading text-xl font-semibold">Hinweise</h2>
                                <div class="mt-4 space-y-3 text-sm text-[#c9bfb3]">
                                    <p class="barber-panel-muted p-3">
                                        Wenn keine Zeiten angezeigt werden, ist der Tag geschlossen oder bereits voll belegt.
                                    </p>
                                    <p class="barber-panel-muted p-3">
                                        Bereits angefragte Slots sind sichtbar, aber nicht mehr auswählbar.
                                    </p>
                                </div>
                            </section>
                        </div>
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
            const slotSelectionMessage = document.getElementById('slot-selection-message');
            const startTimeEl = document.getElementById('start_time');
            const endTimeEl = document.getElementById('end_time');
            const slotConfirmedEl = document.getElementById('slot_confirmed');
            const bookingForm = document.getElementById('booking-form');
            const availabilityUrl = "{{ url('/availability') }}";

            // Setzt den Slotbereich und die versteckten Felder nach Aenderungen zurueck.
            function clearSlotSelection() {
                startTimeEl.value = '';
                endTimeEl.value = '';
                slotConfirmedEl.value = '';
                slotsContainer.innerHTML = '';
                slotSelectionMessage.textContent = '';
            }

            // Schreibt Statushinweise unter den Slotbereich.
            function renderMessage(message) {
                slotsMessage.textContent = message;
            }

            // Markiert den gewaehlten Slot-Button und speichert die Zeiten in Hidden Inputs.
            function selectSlot(button, slot) {
                document.querySelectorAll('#slots-container button').forEach(function (item) {
                    item.classList.remove('btn-primary');
                    item.classList.add('btn-outline');
                });

                button.classList.remove('btn-outline');
                button.classList.add('btn-primary');
                startTimeEl.value = slot.start_time;
                endTimeEl.value = slot.end_time;
                slotConfirmedEl.value = '1';
                slotSelectionMessage.textContent = '';
            }

            // Rendert freie und bereits angefragte Slots in derselben Grid-Struktur.
            function renderSlots(slots) {
                clearSlotSelection();

                if (!slots || slots.length === 0) {
                    renderMessage('Keine freien Slots an diesem Tag.');
                    return;
                }

                renderMessage('Bitte wählen Sie ein freies Zeitfenster.');

                slots.forEach(function (slot) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    const isRequested = slot.status === 'requested';

                    if (isRequested) {
                        button.className = 'btn btn-disabled w-full';
                        button.textContent = slot.start_time + ' - ' + slot.end_time + ' (Angefragt)';
                        button.disabled = true;
                    } else {
                        button.className = 'btn btn-outline w-full';
                        button.textContent = slot.start_time + ' - ' + slot.end_time;
                        button.addEventListener('click', function () {
                            selectSlot(button, slot);
                        });
                    }

                    slotsContainer.appendChild(button);
                });
            }

            // Laedt Verfuegbarkeiten passend zu Service + Datum vom bestehenden Endpoint.
            async function loadSlots() {
                const serviceId = serviceEl.value;
                const date = dateEl.value;

                clearSlotSelection();

                if (!serviceId || !date) {
                    renderMessage('Bitte zuerst Service und Datum wählen.');
                    return;
                }

                renderMessage('Slots werden geladen...');

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
                    renderMessage('Netzwerkfehler beim Laden der Slots.');
                }
            }

            serviceEl.addEventListener('change', loadSlots);
            dateEl.addEventListener('change', loadSlots);

            // Verhindert veraltete Browserwerte nach Zurueck-Button / Autofill.
            serviceEl.value = '';
            dateEl.value = '';
            clearSlotSelection();
            renderMessage('Bitte zuerst Service und Datum wählen.');

            // Verhindert Submit ohne aktiv gewaehlten Slot.
            bookingForm.addEventListener('submit', function (event) {
                if (!startTimeEl.value || !endTimeEl.value || slotConfirmedEl.value !== '1') {
                    event.preventDefault();
                    slotSelectionMessage.textContent = 'Bitte zuerst ein Zeitfenster auswählen.';
                }
            });
        });
    </script>
</body>
</html>
