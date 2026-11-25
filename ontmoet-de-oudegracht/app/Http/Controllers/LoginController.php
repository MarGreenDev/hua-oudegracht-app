<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate(
            [
                'name' => 'required',
                'password' => 'required',
            ],
            [
                'name.required' => 'Please fill in your username!',
                'password.required' => 'Please fill in your password!',
            ]
        );

        if (\Auth::attempt($credentials)) {

            $request->session()->regenerate();

            return redirect('/cms');
        }

        return back()->withErrors([
            'name' => 'Incorrect username or password!! (；′⌒`)'
        ]);
    }

    public function logout(Request $request) {
        \Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerate();

        return redirect('/');
    }

}
