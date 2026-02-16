<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        // Validierung der Eingabedaten
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required_without:phone|nullable',
            'phone' => 'required_without:email|nullable',
        ]);

        // Kundendaten speichern (hier könnte die Logik zum Speichern in der Datenbank stehen)
        \App\Models\Customer::create($validatedData);

        // Nach erfolgreicher Speicherung zurück zur Welcome-Seite mit Erfolgsmeldung
        return redirect()->route('welcome')->with('success', 'Kundendaten erfolgreich gespeichert!');
    }
}
