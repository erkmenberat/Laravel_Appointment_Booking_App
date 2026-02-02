<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

// Login and Registration Routes
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

// Dashboard Route (Protected)
//ja wirklich

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');  

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/'); // oder route('login')
})->name('logout');