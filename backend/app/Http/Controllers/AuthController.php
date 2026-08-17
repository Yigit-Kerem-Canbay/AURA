<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            \App\Models\ActionLog::create([
                'user_id' => Auth::id(),
                'action' => 'login',
                'ip_address' => $request->ip(),
                'details' => 'Başarılı giriş yapıldı.'
            ]);
            
            return redirect()->intended('/');
        }
        
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            \App\Models\ActionLog::create([
                'user_id' => $user->id,
                'action' => 'failed_login',
                'ip_address' => $request->ip(),
                'details' => 'Hatalı şifre denemesi.'
            ]);
        }

        return back()->withErrors([
            'email' => 'Sağlanan kimlik bilgileri kayıtlarımızla eşleşmiyor.',
        ])->onlyInput('email');
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
                'details' => 'Çıkış yapıldı.'
            ]);
        }

        return redirect('/login');
    }
}
