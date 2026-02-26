<x-layout title="Admin Registrierung">
    {{-- Intro clarifies that this page creates admin accounts. --}}
    <div class="mb-6">
        <span class="barber-badge">Admin Setup</span>
        <h1 class="barber-heading mt-3 text-3xl font-semibold">Registrierung</h1>
        <p class="mt-2 text-sm text-[#c9bfb3]">
            Neues Admin-Konto anlegen, um Anfragen freizugeben und Termine zu verwalten.
        </p>
    </div>

    {{-- Validation block is shared in style with login and other views. --}}
    @if ($errors->any())
        <div class="alert alert-error mb-4">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Registration form keeps existing backend route names unchanged. --}}
    <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="label"><span class="label-text">Name</span></label>
            <input id="name" name="name" type="text" class="input input-bordered w-full" value="{{ old('name') }}" required>
        </div>

        <div>
            <label for="email" class="label"><span class="label-text">E-Mail</span></label>
            <input id="email" name="email" type="email" class="input input-bordered w-full" value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password" class="label"><span class="label-text">Passwort</span></label>
            <input id="password" name="password" type="password" class="input input-bordered w-full" required>
        </div>

        <div>
            <label for="password_confirmation" class="label"><span class="label-text">Passwort bestaetigen</span></label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="input input-bordered w-full" required>
        </div>

        {{-- Main action button for account creation. --}}
        <button class="btn btn-primary w-full" type="submit">Admin-Konto erstellen</button>
    </form>

    {{-- Back-link to login for existing admins. --}}
    <p class="mt-5 text-center text-sm text-[#c9bfb3]">
        Bereits registriert?
        <a href="{{ route('login') }}" class="text-[#f0c08f] underline-offset-4 hover:underline">Zum Login</a>
    </p>
</x-layout>
