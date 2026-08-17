<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AURA - Kayıt Ol</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app-f90138d3.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center py-10">
    <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-slate-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">AURA</h1>
            <p class="text-sm text-slate-500 mt-2">Sisteme Katılın</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-600 text-sm border border-red-100">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Ad Soyad</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">E-posta Adresi</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Şifre</label>
                <input type="password" id="password" name="password" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Şifre (Tekrar)</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-medium py-2.5 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm mt-2">
                Kayıt Ol
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Zaten hesabınız var mı? <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">Giriş Yap</a>
        </p>
    </div>
</body>
</html>
