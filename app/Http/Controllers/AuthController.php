<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function showLoginForm(): RedirectResponse|View
    {
        if (session('jwt_token')) {
            try {
                if (JWTAuth::setToken(session('jwt_token'))->authenticate()) {
                    return redirect()->route('dashboard');
                }
            } catch (\Throwable) {
                session()->forget('jwt_token');
            }
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! $token = JWTAuth::attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Invalid email or password.');
        }

        session(['jwt_token' => $token]);

        return redirect()->route('dashboard')->with('success', 'Logged in successfully.');
    }

    public function logout(): RedirectResponse
    {
        if ($token = session('jwt_token')) {
            try {
                JWTAuth::setToken($token)->invalidate();
            } catch (\Throwable) {
                // The session still needs to be cleared if the token is already invalid.
            }
        }

        session()->forget('jwt_token');
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showProfile(Request $request): View
    {
        return view('auth.profile', [
            'user' => $request->attributes->get('jwt_user'),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->attributes->get('jwt_user');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:new_password,password_confirmation'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($request->filled('new_password') && ! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withInput($request->only('name', 'email'))
                ->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('new_password')) {
            $user->password = $validated['new_password'];
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
}
