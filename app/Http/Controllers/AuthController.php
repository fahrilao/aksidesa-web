<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Try to authenticate with email first, then with username
        $loginField = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        $authCredentials = [
            $loginField => $credentials['email'],
            'password' => $credentials['password']
        ];

        if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Block RW users from logging in through website
            if ($user->role === 'RW') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'RW users cannot login through the website. Please use the mobile application.',
                ]);
            }
            
            $request->session()->regenerate();
            
            return redirect()->intended(route('home'));
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('auth.login');
    }
}
