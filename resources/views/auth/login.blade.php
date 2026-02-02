<x-layout>
    <form action="{{ route('login.attempt') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="E-Mail">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Submit</button>
    </form>
</x-layout>

