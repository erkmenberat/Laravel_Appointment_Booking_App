<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        // Entweder E-Mail oder Telefon ist Pflicht; beide Felder bleiben eindeutig.
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'required_without:phone', Rule::unique('customers', 'email')],
            'phone' => ['nullable', 'string', 'max:30', 'required_without:email', Rule::unique('customers', 'phone')],
            'note' => 'nullable|string',
        ]);

        \App\Models\Customer::create($validatedData);

        return redirect()->route('welcome')->with('success', 'Kundendaten erfolgreich gespeichert!');
    }
}
