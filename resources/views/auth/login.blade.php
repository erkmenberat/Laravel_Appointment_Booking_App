<x-layout title="Admin Login">
    {{-- Intro explains the page purpose and keeps the design consistent with the new theme. --}}
    <div class="mb-6">
        <span class="barber-badge">Admin Bereich</span>
        <h1 class="barber-heading mt-3 text-3xl font-semibold">Login</h1>
        <p class="mt-2 text-sm text-[#c9bfb3]">
            Melde dich an, um Termine zu bestaetigen, zu bearbeiten und Verfuegbarkeiten zu pruefen.
        </p>
    </div>

    {{-- Validation errors stay directly above the form for immediate feedback. --}}
    @if ($errors->any())
        <div class="alert alert-error mb-4">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Login form keeps route + CSRF logic, only the design and structure changed. --}}
    <form action="{{ route('login.attempt') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="label"><span class="label-text">E-Mail</span></label>
            <input
                id="email"
                name="email"
                type="email"
                class="input input-bordered w-full"
                value="{{ old('email') }}"
                placeholder="admin@example.com"
                required
            >
        </div>

        <div>
            <label for="password" class="label"><span class="label-text">Passwort</span></label>
            <input
                id="password"
                name="password"
                type="password"
                class="input input-bordered w-full"
                placeholder="Passwort eingeben"
                required
            >
        </div>

        {{-- Primary CTA uses the copper accent color from the logo. --}}
        <button type="submit" class="btn btn-primary w-full">Einloggen</button>
    </form>

    {{-- Hinweis: Registrierung ist nur im Admin-Dashboard verfuegbar. --}}
    <p class="mt-5 text-center text-sm text-[#c9bfb3]">
        Neue Admin-Konten können nur im Admin-Dashboard erstellt werden.
    </p>
</x-layout>
