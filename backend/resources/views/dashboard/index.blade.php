@extends('layouts.app')

@section('header', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Dokümanlar Kartı -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">Toplam Doküman</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $documentCount }}</p>
        </div>
        <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
    </div>

    <!-- AI Soruları Kartı -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">AI Soruları</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $aiQuestionCount }}</p>
        </div>
        <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        </div>
    </div>

    <!-- Web Siteleri Kartı -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">Taranan Siteler</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $websiteCount }}</p>
        </div>
        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
        </div>
    </div>

    <!-- Audit Raporları Kartı -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">Denetim Raporu</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $auditCount }}</p>
        </div>
        <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 text-center">
    <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
    </div>
    <h3 class="text-xl font-bold text-slate-800">AURA'ya Hoş Geldiniz</h3>
    <p class="text-slate-500 mt-2 max-w-md mx-auto">Sistem özeti ve grafikler yakında bu alanda yer alacaktır. Sol menüden modüllere erişebilirsiniz.</p>
</div>
@endsection
