<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Verarbeitet die Registrierung ueber eine einzelne Invokable-Methode.
 */
class RegisterController extends Controller
{
    /**
     * Validiert Formulardaten, erstellt den Benutzer und leitet weiter.
     */
    public function __invoke(Request $request)
    {
        // 1) Eingabedaten pruefen
        $userdata = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // 2) Passwort hashen und nur das gehashte Feld speichern
        $userdata['password'] = Hash::make($userdata['password']);

        // 3) Datensatz speichern
        $user = \App\Models\User::create($userdata);

        // 4) Einfache Erfolgs-/Fallback-Weiterleitung
        if ($user !== null) {
            return redirect()->route('login');
        }

        return redirect()->route('dashboard');
    }
}
