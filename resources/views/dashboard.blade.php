<x-layout>
<div>
    <h1>WELCOME TO DASHBOARD {{ Auth::user()->name }}</h1>
    <form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">
        Logout
    </button>
</form>

</div>
</x-layout>



