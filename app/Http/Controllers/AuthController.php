<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard');
            } elseif ($user->role === 'pustakawan') {
                return redirect()->route('pustakawan.dashboard');
            } else {
                return redirect()->route('member.dashboard');
            }
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'superadmin') {
                return redirect()->intended(route('superadmin.dashboard'));
            } elseif ($user->role === 'pustakawan') {
                return redirect()->intended(route('pustakawan.dashboard'));
            } else {
                return redirect()->intended(route('member.dashboard'));
            }
        }

        return back()->withErrors([
            'email' => 'Alamat email atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem DigiLib.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'member',
            'status' => 'active',
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran akun berhasil! Silakan masuk menggunakan email dan kata sandi Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
