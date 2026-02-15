<x-layout>
<div>
    {{-- Dashboard-Startseite für angemeldete Nutzer --}}
    <h1>WELCOME TO DASHBOARD {{ Auth::user()->name }}</h1>

    {{-- Formular für den sicheren Logout (POST + CSRF) --}}
    <form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">
        Logout
    </button>
</form>

</div>
</x-layout>


