<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        // If user is already logged in, redirect to POS
        if (Auth::check()) {
            Log::info('User already logged in, redirecting to POS', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email
            ]);
            return redirect('/pos');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        Log::info('Login attempt', [
            'email' => $credentials['email'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Check if user exists
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            Log::warning('Login failed - user not found', [
                'email' => $credentials['email']
            ]);
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Check password
        if (!Hash::check($credentials['password'], $user->password)) {
            Log::warning('Login failed - invalid password', [
                'email' => $credentials['email'],
                'user_id' => $user->id
            ]);
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Attempt authentication
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            Log::info('Login successful', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'user_name' => Auth::user()->name,
                'job_role' => Auth::user()->job_role
            ]);

            // Redirect to POS system
            return redirect()->intended('/pos');
        }

        Log::warning('Login failed - authentication failed', [
            'email' => $credentials['email'],
            'ip' => $request->ip()
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        Log::info('User logout', [
            'user_id' => $user?->id,
            'user_email' => $user?->email
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function dashboard()
    {
        $user = Auth::user();
        return view('auth.dashboard', compact('user'));
    }
}
