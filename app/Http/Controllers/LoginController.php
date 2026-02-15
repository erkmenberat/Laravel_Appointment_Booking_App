<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Verarbeitet den Login über eine einzelne Invokable-Methode.
 */
class LoginController extends Controller
{
    /**
     * Validiert Zugangsdaten und meldet den Benutzer an.
     */
    public function __invoke(Request $request)
    {
        // 1) Eingaben prüfen
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        // 2) Authentifizierung versuchen
        if (Auth::attempt($credentials)) {
            // Session aus Sicherheitsgründen erneuern
            $request->session()->regenerate();
 
            return redirect()->intended('dashboard');
        }
 
        // 3) Bei Fehler zurück zum Formular
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
