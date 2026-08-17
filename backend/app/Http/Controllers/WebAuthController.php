<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            \App\Models\ActionLog::create([
                'user_id' => Auth::id(),
                'action' => 'login',
                'ip_address' => $request->ip(),
                'details' => 'Başarılı giriş yapıldı (Web).'
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'redirect' => route('dashboard')]);
            }
            return redirect()->intended('/');
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            \App\Models\ActionLog::create([
                'user_id' => $user->id,
                'action' => 'failed_login',
                'ip_address' => $request->ip(),
                'details' => 'Hatalı şifre denemesi (Web).'
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Girdiğiniz e-posta veya şifre hatalı.'
            ], 401);
        }

        return back()->withErrors([
            'email' => 'Girdiğiniz e-posta veya şifre hatalı.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3, // Default role: calisan (assuming 3 is calisan)
        ]);

        Auth::login($user);

        return redirect('/');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($userId) {
            \App\Models\ActionLog::create([
                'user_id' => $userId,
                'action' => 'logout',
                'ip_address' => $request->ip(),
                'details' => 'Çıkış yapıldı (Web).'
            ]);
        }

        return redirect('/login');
    }
}
