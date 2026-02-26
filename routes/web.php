<?php

use App\Http\Controllers\AdminAppointmentController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

Route::get('login', function () {
    return view('auth.login');
})->name('login');

Route::post('login', LoginController::class)->name('login.attempt');

Route::get('register', function () {
    return view('auth.register');
})->name('register');

Route::post('register', RegisterController::class)->name('register.store');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('login');
})->name('logout');

Route::get('welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('customer', function () {
    return view('welcome');
})->name('customer');

Route::post('customer', [CustomerController::class, 'store'])->name('customer.store');
Route::get('availability', [AvailabilityController::class, 'index'])->name('availability.index');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('dashboard', [AdminAppointmentController::class, 'index'])->name('dashboard');
    Route::get('dashboard/appointments/{appointment}/edit', [AdminAppointmentController::class, 'edit'])->name('admin.appointments.edit');
    Route::put('dashboard/appointments/{appointment}', [AdminAppointmentController::class, 'update'])->name('admin.appointments.update');
    Route::post('dashboard/appointments/{appointment}/accept', [AdminAppointmentController::class, 'accept'])->name('admin.appointments.accept');
    Route::post('dashboard/appointments/{appointment}/reject', [AdminAppointmentController::class, 'reject'])->name('admin.appointments.reject');
    Route::post('dashboard/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('admin.appointments.cancel');
});
