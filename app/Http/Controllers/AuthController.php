<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return view('login');
    }

    public function postlogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ], [
            'email.exists' => 'The user does not exist',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('applications.index');
        }

        return redirect()->route('login')->withInput()->withErrors(['password' => 'The provided credentials do not match our records.']);
    }


    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
