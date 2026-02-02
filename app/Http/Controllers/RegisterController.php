<?php

namespace App\Http\Controllers;

use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Exists;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $userdata = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $userdata['passwordhash'] = bcrypt($userdata['password']);
        unset($userdata['password']);
        \App\Models\User::create($userdata);
        if($userdata != null){
            return redirect()->route('login');
        }
        else{
            return redirect()->route('dashboard');
        }
    }
}
