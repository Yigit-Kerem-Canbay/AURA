@extends('layouts.app')

@section('header', 'Profilim')

@section('content')
<div class="max-w-4xl mx-auto mt-8 animate-fade-in-up">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100 relative">
        <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
        <div class="px-8 pb-8">
            <div class="-mt-16 mb-6 flex justify-between items-end">
                <div class="w-32 h-32 rounded-full border-4 border-white bg-white shadow-lg overflow-hidden flex items-center justify-center bg-gradient-to-br from-indigo-100 to-purple-100">
                    <span class="text-5xl font-extrabold text-indigo-700">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                </div>
                <button class="bg-white text-indigo-600 border border-indigo-200 hover:bg-indigo-50 px-6 py-2 rounded-xl font-bold shadow-sm transition-colors">
                    Profili Düzenle
                </button>
            </div>
            
            <h2 class="text-3xl font-bold text-slate-800">{{ Auth::user()->name ?? 'Kullanıcı Adı' }}</h2>
            <p class="text-indigo-600 font-semibold mt-1">{{ Auth::user()->role->name ?? 'Çalışan' }}</p>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Hesap Bilgileri -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Hesap Bilgileri
                    </h3>
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="text-slate-400 block mb-1">E-posta Adresi</span>
                            <span class="font-medium text-slate-800">{{ Auth::user()->email ?? 'email@example.com' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Kayıt Tarihi</span>
                            <span class="font-medium text-slate-800">{{ Auth::user()->created_at ? Auth::user()->created_at->format('d M Y') : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Güvenlik -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Güvenlik
                    </h3>
                    <form action="#" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <input type="password" placeholder="Mevcut Şifre" class="w-full text-sm px-4 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <input type="password" placeholder="Yeni Şifre" class="w-full text-sm px-4 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <button type="button" class="w-full bg-slate-800 text-white font-bold py-2 rounded-xl hover:bg-slate-700 transition">
                            Şifreyi Güncelle
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
