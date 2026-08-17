<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AURA - AI Unified Research & Audit</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .bg-mesh {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 40% 20%, hsla(220,100%,74%,0.15) 0px, transparent 50%),
                radial-gradient(at 80% 0%, hsla(189,100%,56%,0.15) 0px, transparent 50%),
                radial-gradient(at 0% 50%, hsla(355,100%,93%,0.1) 0px, transparent 50%);
        }
    </style>
</head>
<body class="font-sans antialiased bg-mesh text-slate-800 min-h-screen selection:bg-indigo-200 selection:text-indigo-900">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Premium Sidebar -->
        <aside class="w-72 glass-panel m-4 rounded-3xl shadow-2xl shadow-indigo-900/10 flex flex-col relative z-20 border-r-0">
            <div class="h-24 flex items-center px-8 border-b border-white/50">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 mr-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h1 class="text-2xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight">
                    AURA
                </h1>
            </div>

            <nav class="flex-1 px-4 py-8 space-y-3 overflow-y-auto">
                <p class="px-4 text-xs font-bold tracking-wider text-slate-400 uppercase mb-4">Ana Menü</p>
                
                <a href="/" class="flex items-center px-4 py-3.5 rounded-2xl transition-all duration-300 {{ request()->is('/') ? 'bg-white shadow-md text-indigo-700 shadow-indigo-100/50 scale-105' : 'text-slate-500 hover:bg-white/50 hover:text-indigo-600' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->is('/') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="font-semibold">Dashboard</span>
                </a>

                <a href="/documents" class="flex items-center px-4 py-3.5 rounded-2xl transition-all duration-300 {{ request()->is('documents') ? 'bg-white shadow-md text-indigo-700 shadow-indigo-100/50 scale-105' : 'text-slate-500 hover:bg-white/50 hover:text-indigo-600' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->is('documents') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    <span class="font-semibold">Dokümanlar</span>
                </a>

                @if(Auth::user()->isAdmin())
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 transition-all {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700/50 hover:text-gray-900 dark:hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="font-medium">Kullanıcılar</span>
                </a>
                @endif

                <a href="/chat" class="flex items-center px-4 py-3.5 rounded-2xl transition-all duration-300 {{ request()->is('chat') ? 'bg-white shadow-md text-indigo-700 shadow-indigo-100/50 scale-105' : 'text-slate-500 hover:bg-white/50 hover:text-indigo-600' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->is('chat') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    <span class="font-semibold">AURA Chat <span class="ml-2 text-[10px] bg-gradient-to-r from-purple-500 to-indigo-500 text-white px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">RAG</span></span>
                </a>

                <a href="/audit" class="flex items-center px-4 py-3.5 rounded-2xl transition-all duration-300 {{ request()->is('audit') ? 'bg-white shadow-md text-indigo-700 shadow-indigo-100/50 scale-105' : 'text-slate-500 hover:bg-white/50 hover:text-indigo-600' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->is('audit') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span class="font-semibold">Website Audit</span>
                </a>
            </nav>

            <div class="p-6 border-t border-white/50" id="profileMenuContainer">
                <!-- Dropdown (Opens upwards natively) -->
                <div id="profileDropdown" class="hidden w-full bg-white rounded-xl shadow-lg border border-slate-100 py-2 mb-2">
                    <div class="px-4 py-2 border-b border-slate-50 mb-1">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Hesap İşlemleri</p>
                    </div>
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-indigo-50 hover:text-indigo-700 font-medium">Profilim</a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-1 border-t border-slate-50 pt-1" id="logoutForm">
                        @csrf
                        <button type="submit" onclick="this.disabled=true; this.form.submit(); this.innerHTML='<svg class=\'animate-spin w-4 h-4 mr-2 inline\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg>Çıkılıyor...'" class="w-full text-left px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50 hover:text-red-700 transition">
                            Çıkış Yap
                        </button>
                    </form>
                </div>

                <!-- User Profile Button -->
                <button id="profileMenuBtn" class="w-full flex items-center gap-3 relative group focus:outline-none rounded-xl p-3 hover:bg-white/60 transition-all shadow-sm border border-transparent hover:border-white">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200 shadow-sm">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="hidden sm:block text-left flex-1">
                        <div class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ Auth::user()->name ?? 'Kullanıcı' }}</div>
                        <div class="text-xs font-medium text-slate-500">{{ Auth::user()->role->name ?? 'Çalışan' }}</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500 transition-transform" id="profileChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col relative z-10 overflow-hidden">
            <!-- Header -->
            <header class="h-24 flex items-center justify-between px-10">
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                    @yield('header')
                </h2>
                <div class="flex items-center space-x-4">
                    <button class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto px-10 pb-10">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const profileBtn = document.getElementById('profileMenuBtn');
            const profileDropdown = document.getElementById('profileDropdown');

            if (profileBtn && profileDropdown) {
                profileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', () => {
                    if (!profileDropdown.classList.contains('hidden')) {
                        profileDropdown.classList.add('hidden');
                    }
                });

                profileDropdown.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }
        });
    </script>
</body>
</html>
