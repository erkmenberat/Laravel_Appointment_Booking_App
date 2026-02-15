<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;

// Startseite: Eingeloggte Nutzer direkt ins Dashboard leiten.
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

// Authentifizierung: Login und Registrierung
Route::get('login', function () {
    return view('auth.login');
})->name('login');

Route::post('login', LoginController::class)->name('login.attempt');

Route::get('register', function () {
    return view('auth.register');
})->name('register');

Route::post('register', RegisterController::class)->name('register.store');

Route::get('adminlogin', function () {
    return view('auth.adminlogin');
})->name('adminlogin');

Route::post('adminlogin', LoginController::class)->name('adminlogin.attempt');

// Geschützter Bereich: nur für authentifizierte Nutzer.
Route::get('dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');  

// Logout: Session beenden und Token neu erzeugen.
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/'); // Alternativ: route('login')
})->name('logout');


// Direkte Route auf die Welcome-Seite.
Route::get('welcome', function () {
    return view('welcome');
})->name('welcome');


