<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user || !$user->is_active) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Dieses Konto ist deaktiviert.',
                ])->onlyInput('email');
            }

            if ($user->role !== 'admin') {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Kein Admin-Zugriff. Bitte mit einem Admin-Konto anmelden.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Die Zugangsdaten sind falsch.',
        ])->onlyInput('email');
    }
}