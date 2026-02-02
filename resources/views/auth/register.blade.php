<x-layout>
<div>
    <form action="{{ route('register.store') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Name">
        <input type="email" name="email" placeholder="E-Mail">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Register</button>
    </form>
</div>
</x-layout>
