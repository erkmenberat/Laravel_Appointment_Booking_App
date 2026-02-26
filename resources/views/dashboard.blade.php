<x-layout title="Dashboard" mode="wide">
    {{-- Page header shows the logged-in user and documents that the theme is now shared. --}}
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <span class="barber-badge">Dashboard</span>
            <h1 class="barber-heading mt-3 text-3xl font-semibold">Willkommen, {{ Auth::user()->name }}</h1>
            <p class="mt-2 text-sm text-[#c9bfb3]">
                Diese Startseite nutzt jetzt dasselbe Barber-Design wie Login, Booking und Admin-Bereich.
            </p>
        </div>
        <a href="{{ route('welcome') }}" class="btn btn-outline">Zur Terminseite</a>
    </div>

    {{-- Card layout is a clean base for future dashboard widgets. --}}
    <div class="grid gap-4 md:grid-cols-2">
        <section class="barber-panel-muted p-4">
            <h2 class="barber-heading text-xl font-semibold">Status</h2>
            <p class="mt-2 text-sm text-[#c9bfb3]">
                Du bist erfolgreich eingeloggt. Diese Flaeche kann spaeter mit Kennzahlen erweitert werden.
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="barber-chip">Theme aktiv</span>
                <span class="barber-chip">Responsive</span>
                <span class="barber-chip">Kommentiert</span>
            </div>
        </section>

        <section class="barber-panel-muted p-4">
            <h2 class="barber-heading text-xl font-semibold">Sitzung</h2>
            <p class="mt-2 text-sm text-[#c9bfb3]">
                Logout bleibt bewusst ein POST-Formular mit CSRF-Schutz.
            </p>

            {{-- Secure logout: POST + CSRF instead of a plain GET link. --}}
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-primary w-full md:w-auto">Logout</button>
            </form>
        </section>
    </div>
</x-layout>
