<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $userdata = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $userdata['password'] = Hash::make($userdata['password']);
        $userdata['role'] = 'admin';

        $user = User::create($userdata);

        if ($user !== null) {
            return redirect()->route('dashboard')->with('success', 'Neues Admin-Konto wurde erstellt.');
        }

        return back()->withErrors([
            'email' => 'Das Admin-Konto konnte nicht erstellt werden.',
        ])->withInput();
    }
}
