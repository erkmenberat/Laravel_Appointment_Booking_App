<?php

namespace App\Http\Controllers;

use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Exists;

/**
 * Verarbeitet die Registrierung über eine einzelne Invokable-Methode.
 */
class RegisterController extends Controller
{
    /**
     * Validiert Formulardaten, erstellt den Benutzer und leitet weiter.
     */
    public function __invoke(Request $request)
    {
        // 1) Eingabedaten prüfen
        $userdata = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2) Passwort hashen und Rohwert entfernen
        $userdata['passwordhash'] = bcrypt($userdata['password']);
        unset($userdata['password']);

        // 3) Datensatz speichern
        \App\Models\User::create($userdata);

        // 4) Einfache Erfolgs-/Fallback-Weiterleitung
        if($userdata != null){
            return redirect()->route('login'); 
        }
        else{
            return redirect()->route('dashboard');
        }
    }
}
