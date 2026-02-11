<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientFormController extends Controller
{
    //
    public function __invoke(Request $request)
    {
        $patientData = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email'],
            'note' => ['nullable', 'string']
        ]);

        Patient::create($patientData);
        return redirect()->route('welcome');
    }
}
