@extends('layouts.app')

@section('header', 'Web Sitesi Geçmişi: ' . $website->url)

@section('content')
@php
    $scans = $website->scans;
    $latestScan = $scans->first();
    // Reverse for chronological order in chart (oldest to newest)
    $chartScans = $scans->where('status', 'completed')->reverse()->values();
@endphp

<div class="max-w-7xl mx-auto space-y-8 animate-fade-in-up">
    
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('audit.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Tüm Sitelere Dön
        </a>
    </div>
    <div class="bg-white/70 backdrop-blur-lg rounded-3xl shadow-xl border border-white/50 p-10 flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="relative z-10">
            <h2 class="text-3xl font-bold text-slate-800 mb-2">Web Sitesi Denetim Geçmişi</h2>
            <p class="text-indigo-600 font-bold text-xl">{{ $website->url }}</p>
            <div class="mt-4 flex items-center space-x-4 text-sm text-slate-400 font-medium">
                <span>İlk Eklenme: {{ $website->created_at->format('d M Y') }}</span>
                <span>Toplam Tarama: {{ $scans->count() }}</span>
            </div>
        </div>
        
        <div class="mt-6 md:mt-0 flex items-center justify-center relative z-10">
            <form action="{{ route('audit.store') }}" method="POST" id="reScanForm">
                @csrf
                <input type="hidden" name="url" value="{{ $website->url }}">
                <button type="submit" id="reScanBtn" onclick="document.getElementById('reScanBtn').classList.add('hidden'); document.getElementById('reScanLoading').classList.remove('hidden');" class="bg-gradient-to-r from-indigo-600 to-fuchsia-600 hover:from-indigo-700 hover:to-fuchsia-700 text-white font-bold py-3 px-8 rounded-full shadow-md hover:shadow-lg transition-all transform hover:scale-105 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Yeniden Tara
                </button>
                <button type="button" id="reScanLoading" disabled class="hidden bg-slate-300 text-slate-500 font-bold py-3 px-8 rounded-full shadow-inner flex items-center cursor-not-allowed">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Taranıyor...
                </button>
            </form>
        </div>
    </div>

    <!-- CHART.JS GRAFIĞI -->
    @if($chartScans->count() > 0)
    <div class="bg-white rounded-3xl p-8 shadow-md border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                Gelişim Grafiği (Son Taramalar)
            </h3>
            @if($chartScans->count() == 1)
            <span class="text-sm text-slate-500 bg-slate-50 px-3 py-1 rounded-full border border-slate-100 mt-2 md:mt-0">
                <svg class="w-4 h-4 inline-block mr-1 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Grafiğin oluşması için sitenizi yeniden taratın.
            </span>
            @endif
        </div>
        <div class="w-full h-80 relative">
            <canvas id="historyChart"></canvas>
        </div>
    </div>
    @endif

    <!-- LATEST SCAN DETAILS -->
    @if($latestScan)
        @if($latestScan->status == 'completed' || $latestScan->status == 'işlendi')
            <div class="bg-white rounded-3xl p-8 shadow-md border border-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        En Son Tarama Raporu
                    </h3>
                    <span class="text-sm text-slate-500 font-medium">{{ $latestScan->created_at->format('d M Y, H:i') }}</span>
                </div>

                <!-- AI Summary -->
                @if($latestScan->ai_summary)
                <div class="bg-gradient-to-r from-indigo-500/10 to-fuchsia-500/10 rounded-2xl p-6 border border-indigo-100 mb-8">
                    <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-widest flex items-center mb-3">
                        <svg class="w-5 h-5 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Yapay Zeka Özeti
                    </h4>
                    <div class="text-slate-700 leading-relaxed font-medium text-sm">
                        {!! nl2br(e($latestScan->ai_summary)) !!}
                    </div>
                </div>
                @endif

                <!-- Category Scores -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    @php
                        $categories = [
                            ['name' => 'SEO', 'score' => $latestScan->seo_score, 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'color' => 'blue'],
                            ['name' => 'Güvenlik', 'score' => $latestScan->security_score, 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'color' => 'emerald'],
                            ['name' => 'Performans', 'score' => $latestScan->performance_score, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'amber'],
                            ['name' => 'Erişilebilirlik', 'score' => $latestScan->accessibility_score, 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'purple']
                        ];
                    @endphp

                    @foreach($categories as $cat)
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ $cat['name'] }}</p>
                            <p class="text-2xl font-extrabold text-slate-700">{{ $cat['score'] ?? 0 }}<span class="text-sm text-slate-400">/100</span></p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-{{ $cat['color'] }}-100 flex items-center justify-center text-{{ $cat['color'] }}-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cat['icon'] }}"></path></svg>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Detailed Issues -->
                <div>
                    <h4 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-4">Tespit Edilen Son Sorunlar</h4>
                    <div class="space-y-4">
                        @php
                            $issues = is_array($latestScan->report_data) && isset($latestScan->report_data['issues']) ? $latestScan->report_data['issues'] : [];
                            $severities = [
                                'critical' => ['label' => 'Kritik', 'bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-500'],
                                'high' => ['label' => 'Yüksek', 'bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'dot' => 'bg-orange-500'],
                            ];
                        @endphp
                        @foreach($severities as $key => $style)
                            @if(isset($issues[$key]) && count($issues[$key]) > 0)
                            <div>
                                <h5 class="text-xs font-bold uppercase text-slate-500 mb-2 flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }} mr-1.5"></span>
                                    {{ $style['label'] }} Seviye Hatalar ({{ count($issues[$key]) }})
                                </h5>
                                <ul class="space-y-1.5">
                                    @foreach($issues[$key] as $issue)
                                    <li class="{{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }} border rounded-md px-3 py-2 text-xs font-medium">
                                        {{ $issue }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif($latestScan->status == 'failed')
            <div class="bg-red-50 border border-red-200 rounded-3xl p-8">
                <h3 class="text-xl font-bold text-red-700 mb-2">Son Tarama Başarısız Oldu</h3>
                <p class="text-red-600 text-sm font-medium">{{ $latestScan->error_message }}</p>
            </div>
        @else
            <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-slate-100">
                <div class="w-16 h-16 mx-auto bg-amber-50 rounded-full flex items-center justify-center text-amber-500 mb-4 animate-pulse">
                    <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Tarama Devam Ediyor...</h3>
                <p class="text-slate-500 text-sm">Site şu an AURA yapay zekası tarafından inceleniyor. Lütfen birkaç saniye sonra sayfayı yenileyin.</p>
            </div>
        @endif
    @endif
</div>

@if($chartScans->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('historyChart').getContext('2d');
        
        const labels = {!! json_encode($chartScans->map(fn($s) => $s->created_at->format('d M H:i'))->toArray()) !!};
        const seoData = {!! json_encode($chartScans->pluck('seo_score')->toArray()) !!};
        const perfData = {!! json_encode($chartScans->pluck('performance_score')->toArray()) !!};
        const secData = {!! json_encode($chartScans->pluck('security_score')->toArray()) !!};
        const accData = {!! json_encode($chartScans->pluck('accessibility_score')->toArray()) !!};
        
        // Gradients
        const blueGradient = ctx.createLinearGradient(0, 0, 0, 400);
        blueGradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
        blueGradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
        
        const greenGradient = ctx.createLinearGradient(0, 0, 0, 400);
        greenGradient.addColorStop(0, 'rgba(16, 185, 129, 0.5)');
        greenGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'SEO',
                        data: seoData,
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    },
                    {
                        label: 'Güvenlik',
                        data: secData,
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    },
                    {
                        label: 'Performans',
                        data: perfData,
                        backgroundColor: '#f59e0b',
                        borderRadius: 4
                    },
                    {
                        label: 'Erişilebilirlik',
                        data: accData,
                        backgroundColor: '#a855f7',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 25,
                            font: { family: "'Inter', sans-serif", weight: '600', size: 13 },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(15, 23, 42, 0.8)',
                        titleFont: { family: "'Inter', sans-serif", size: 13 },
                        bodyFont: { family: "'Inter', sans-serif", size: 13 },
                        padding: 12,
                        cornerRadius: 12,
                        backdropFilter: 'blur(4px)',
                        boxPadding: 6
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        grid: { borderDash: [5, 5], color: '#f1f5f9', drawBorder: false },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#94a3b8', padding: 10 }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#94a3b8', padding: 10 }
                    }
                },
                interaction: {
                    mode: 'index',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    });
</script>
@endif

<style>
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.4s ease-out forwards;
    }
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        50% { transform: translate(-20px, 20px) scale(1.1); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 8s infinite;
    }
</style>

@if($latestScan && $latestScan->status == 'pending')
<script>
    setTimeout(function() {
        window.location.reload();
    }, 5000);
</script>
@endif

@endsection
