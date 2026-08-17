@extends('layouts.app')

@section('header', 'Web Site Denetimi (SEO & Performans)')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in-up">
    
    <!-- Premium Scanner Input -->
    <div class="bg-white/70 backdrop-blur-lg rounded-3xl shadow-xl shadow-purple-900/5 border border-white/50 p-10 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-fuchsia-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

        <div class="relative z-10 max-w-2xl mx-auto text-center">
            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-indigo-500 to-fuchsia-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-indigo-500/30">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-3 tracking-tight">Yeni Bir Siteyi Denetleyin</h3>
            <p class="text-slate-500 mb-8 font-medium">Hedef URL'yi girin; AURA yapay zekası SEO, performans ve erişilebilirlik skorlarını saniyeler içinde çıkarsın.</p>
            
            <form action="{{ route('audit.store') }}" method="POST" class="relative group">
                @csrf
                <div class="flex items-center bg-white border-2 border-slate-200 rounded-full p-2 focus-within:border-indigo-500 focus-within:ring-4 focus-within:ring-indigo-500/20 transition-all shadow-sm group-hover:shadow-md">
                    <div class="pl-4 text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    </div>
                    <input type="url" name="url" required placeholder="https://ornek-site.com" class="flex-1 bg-transparent border-none focus:outline-none px-4 py-3 text-slate-700 font-medium">
                    <button type="submit" class="bg-gradient-to-r from-indigo-600 to-fuchsia-600 hover:from-indigo-700 hover:to-fuchsia-700 text-white font-bold py-3 px-8 rounded-full shadow-md hover:shadow-lg transition-all transform hover:scale-105 active:scale-95">
                        Analizi Başlat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Audits List Section -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-200/50 border border-white/60 overflow-hidden relative z-10">
        <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/50 to-white">
            <h3 class="text-xl font-bold text-slate-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Geçmiş Denetimler
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                        <th class="px-8 py-5">Web Sitesi</th>
                        <th class="px-6 py-5 text-center">Toplam Tarama</th>
                        <th class="px-6 py-5 text-center">Son SEO</th>
                        <th class="px-6 py-5 text-center">Son Performans</th>
                        <th class="px-6 py-5 text-center">Son Tarama</th>
                        <th class="px-8 py-5 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($websites ?? [] as $website)
                        @php
                            /** @var \App\Models\Website $website */
                            $latestScan = $website->scans->first();
                        @endphp
                    <tr class="hover:bg-fuchsia-50/30 transition-colors group">
                        <td class="px-8 py-5 font-semibold text-slate-800 group-hover:text-indigo-700 transition-colors">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center mr-3 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                </div>
                                {{ $website->url }}
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center font-medium text-slate-600">
                            <span class="inline-flex items-center justify-center bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-md text-xs font-bold">
                                {{ $website->scans->count() }} Tarama
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if($latestScan && ($latestScan->status == 'completed' || $latestScan->status == 'işlendi'))
                                <span class="text-sm font-bold {{ ($latestScan->seo_score ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-500' }}">
                                    {{ $latestScan->seo_score ?? 0 }}/100
                                </span>
                            @elseif($latestScan && $latestScan->status == 'pending')
                                <span class="text-xs text-amber-500 flex justify-center items-center"><svg class="animate-spin mr-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> İnceleniyor</span>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if($latestScan && ($latestScan->status == 'completed' || $latestScan->status == 'işlendi'))
                                <span class="text-sm font-bold {{ ($latestScan->performance_score ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-500' }}">
                                    {{ $latestScan->performance_score ?? 0 }}/100
                                </span>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center text-sm font-medium text-slate-500">
                            {{ $latestScan ? $latestScan->created_at->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="px-8 py-5 text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('audit.show', $website->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-indigo-50 to-fuchsia-50 border border-indigo-100 rounded-lg text-indigo-700 hover:from-indigo-100 hover:to-fuchsia-100 transition-all shadow-sm hover:shadow font-semibold">
                                    Geçmiş ve Grafik
                                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                                </a>
                                <form action="{{ route('audit.destroy', $website->id) }}" method="POST" onsubmit="return confirm('Bu siteyi ve tüm geçmiş tarama raporlarını silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center px-3 py-2 bg-white border border-red-200 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 transition-all shadow-sm hover:shadow" title="Tümünü Sil">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-16 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-4 shadow-inner border border-slate-100">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <p class="text-slate-600 font-bold text-lg">Henüz bir site denetlemediniz.</p>
                            <p class="text-slate-400 text-sm mt-2">Yukarıdaki alana bir web adresi girerek SEO, Performans ve Güvenlik geçmişini tutmaya başlayın.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
</style>
@endsection
