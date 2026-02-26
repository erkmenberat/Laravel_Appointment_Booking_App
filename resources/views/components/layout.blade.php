@props([
    'title' => 'Barber Studio',
    'mode' => 'auth', // "auth" = Login/Register Split, "wide" = breiter Contentbereich
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="barber-theme min-h-screen">
    {{-- Globaler Hintergrund bleibt bildbasiert, die Inhalte liegen in klaren Panels darueber. --}}
    <div class="barber-page-shell">
        <div class="barber-page-content">
            {{-- Gemeinsame Navbar: Logo links, Navigation/Aktionen rechts. --}}
            <header class="barber-navbar-shell">
                <div class="barber-navbar barber-panel">
                    <a href="{{ route('welcome') }}" class="barber-navbar-brand">
                        <div class="barber-logo-frame barber-logo-frame--nav">
                            <img src="{{ asset('images/barber-logo.jpg') }}" alt="Barber Logo">
                        </div>
                        <div class="barber-navbar-brand-copy">
                            <div class="barber-navbar-title">Dr. Steinbauer</div>
                            <div class="barber-navbar-subtitle">Barber Terminbuchung</div>
                        </div>
                    </a>

                    {{-- Auth-Links sind direkt erreichbar und bleiben auch mobil brauchbar. --}}
                    <div class="barber-navbar-actions">
                        <a href="{{ route('welcome') }}" class="btn btn-sm btn-outline">Startseite</a>
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline">Login</a>
                    </div>
                </div>
            </header>

            {{-- Hauptbereich unterscheidet zwischen Auth-Split und breiter Inhaltsseite. --}}
            <main class="px-4 py-6 md:px-6 md:py-8">
                @if ($mode === 'wide')
                    <div class="mx-auto max-w-6xl">
                        <section class="barber-panel barber-content-panel barber-content-panel--wide">
                            {{ $slot }}
                        </section>
                    </div>
                @else
                    <div class="barber-auth-shell">
                        {{-- Bildpanel sorgt fuer Markenbezug, ohne das Formular zu ueberladen. --}}
                        <section class="barber-panel barber-photo-card barber-auth-showcase">
                            <img src="{{ asset('images/salon-design.jpg') }}" alt="Salon Design">
                            <div class="barber-photo-overlay">
                                <span class="barber-badge">Barber Studio</span>
                                <h2 class="barber-heading text-2xl font-semibold">Modernes Booking im Salon-Look</h2>
                                <p class="text-sm text-[#e6d8c6]">
                                    Dunkles, warmes Design passend zu eurem Logo und dem Salonfoto.
                                </p>
                            </div>
                        </section>

                        {{-- Formularpanel rendert den Seiteninhalt aus Login/Register. --}}
                        <section class="barber-panel barber-auth-panel">
                            {{ $slot }}
                        </section>
                    </div>
                @endif
            </main>
        </div>
    </div>
</body>
</html>
