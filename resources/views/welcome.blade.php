<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Terminplattform') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 text-base-content">
    <header class="navbar bg-base-100 shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex w-full items-center justify-between">
                <a href="{{ route('welcome') }}" class="text-lg font-semibold">Dr.Steinbauer</a>
                <a href="{{ route('login') }}" class="btn btn-sm md:btn-md">Admin Login</a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-5xl">
            <div class="mb-6">
                <h1 class="text-3xl font-bold">Termin anfragen</h1>
                <p class="mt-2 text-base-content/70">
                    Waehlen Sie Service, Datum und ein verfuegbares Zeitfenster.
                </p>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-4">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="card bg-base-100 shadow-md">
                    <div class="card-body">
                        <h2 class="card-title">Information</h2>
                        <p class="text-sm text-base-content/70">
                            Bitte waehlen Sie zuerst den Service und danach ein Datum.
                            Die verfuegbaren Zeiten werden automatisch geladen.
                        </p>

                        <div class="divider my-2"></div>

                        <div class="space-y-3 text-sm">
                            <div class="rounded-box bg-base-200 p-3">
                                <div class="font-semibold">Ablauf</div>
                                <p class="mt-1 text-base-content/70">
                                    1. Service waehlen
                                    2. Datum waehlen
                                    3. Slot waehlen
                                    4. Kontaktdaten absenden
                                </p>
                            </div>
                            <div class="rounded-box bg-base-200 p-3">
                                <div class="font-semibold">Hinweis</div>
                                <p class="mt-1 text-base-content/70">
                                    Falls keine Zeiten angezeigt werden, ist der Tag geschlossen
                                    oder bereits voll belegt.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card bg-base-100 shadow-md">
                    <div class="card-body">
                        <h2 class="card-title">Formular</h2>

                        <form action="{{ route('customer.store') }}" method="POST" id="booking-form" class="space-y-4">
                            @csrf

                            <div>
                                <label for="service_id" class="label">
                                    <span class="label-text">Service</span>
                                </label>
                                <select name="service_id" id="service_id" class="select select-bordered w-full">
                                    <option value="">Bitte waehlen</option>
                                    @foreach (\App\Models\Service::where('is_active', true)->orderBy('name')->get() as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} ({{ $service->duration }} Min)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="date" class="label">
                                    <span class="label-text">Datum</span>
                                </label>
                                <input
                                    type="date"
                                    id="date"
                                    name="date"
                                    value="{{ old('date') }}"
                                    class="input input-bordered w-full"
                                >
                            </div>

                            <div>
                                <div class="mb-2 font-semibold">Verfuegbare Zeitfenster</div>
                                <div id="slots-container" class="grid grid-cols-2 gap-2"></div>
                                <p id="slots-message" class="mt-2 text-sm text-base-content/70"></p>
                            </div>

                            <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">
                            <input type="hidden" name="end_time" id="end_time" value="{{ old('end_time') }}">

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="first_name" class="label">
                                        <span class="label-text">Vorname</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="first_name"
                                        name="first_name"
                                        value="{{ old('first_name') }}"
                                        class="input input-bordered w-full"
                                    >
                                </div>

                                <div>
                                    <label for="last_name" class="label">
                                        <span class="label-text">Nachname</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="last_name"
                                        name="last_name"
                                        value="{{ old('last_name') }}"
                                        class="input input-bordered w-full"
                                    >
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="phone" class="label">
                                        <span class="label-text">Telefon</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        class="input input-bordered w-full"
                                    >
                                </div>

                                <div>
                                    <label for="email" class="label">
                                        <span class="label-text">E-Mail</span>
                                    </label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        class="input input-bordered w-full"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="note" class="label">
                                    <span class="label-text">Notiz (optional)</span>
                                </label>
                                <textarea
                                    id="note"
                                    name="note"
                                    rows="3"
                                    class="textarea textarea-bordered w-full"
                                >{{ old('note') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-full">Termin anfragen</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const serviceEl = document.getElementById('service_id');
            const dateEl = document.getElementById('date');
            const slotsContainer = document.getElementById('slots-container');
            const slotsMessage = document.getElementById('slots-message');
            const startTimeEl = document.getElementById('start_time');
            const endTimeEl = document.getElementById('end_time');
            const availabilityUrl = "{{ url('/availability') }}";

            function clearSlotSelection() {
                startTimeEl.value = '';
                endTimeEl.value = '';
                slotsContainer.innerHTML = '';
            }

            function renderMessage(message) {
                slotsMessage.textContent = message;
            }

            function selectSlot(button, slot) {
                document.querySelectorAll('#slots-container button').forEach(function (item) {
                    item.classList.remove('btn-primary');
                    item.classList.add('btn-outline');
                });

                button.classList.remove('btn-outline');
                button.classList.add('btn-primary');
                startTimeEl.value = slot.start_time;
                endTimeEl.value = slot.end_time;
            }

            function renderSlots(slots) {
                clearSlotSelection();

                if (!slots || slots.length === 0) {
                    renderMessage('Keine freien Slots an diesem Tag.');
                    return;
                }

                renderMessage('Bitte waehlen Sie ein Zeitfenster.');

                slots.forEach(function (slot) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-outline w-full';
                    button.textContent = slot.start_time + ' - ' + slot.end_time;
                    button.addEventListener('click', function () {
                        selectSlot(button, slot);
                    });
                    slotsContainer.appendChild(button);
                });
            }

            async function loadSlots() {
                const serviceId = serviceEl.value;
                const date = dateEl.value;

                clearSlotSelection();

                if (!serviceId || !date) {
                    renderMessage('Bitte zuerst Service und Datum waehlen.');
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
        });
    </script>
</body>
</html>
