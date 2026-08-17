<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AURA - Giriş Yap</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center">
    <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-slate-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">AURA</h1>
            <p class="text-sm text-slate-500 mt-2">AI Unified Research & Audit</p>
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

        <div id="errorDiv" class="hidden mb-4 p-3 rounded-lg bg-red-50 text-red-600 text-sm border border-red-100 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span id="errorText"></span>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="space-y-5" onsubmit="handleLogin(event)">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">E-posta Adresi</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Şifre</label>
                <input type="password" id="password" name="password" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
            </div>

            <div>
                <button type="submit" id="loginBtn" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition-all">
                    <span id="btnText">Giriş Yap</span>
                    <svg id="btnSpinner" class="hidden animate-spin ml-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>

        <p class="mt-8 text-center text-sm text-slate-500">
            Hesabınız yok mu? <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">Kayıt Ol</a>
        </p>
    </div>

    <script>
        async function handleLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            const text = document.getElementById('btnText');
            const spinner = document.getElementById('btnSpinner');
            const form = e.target;
            const errorDiv = document.getElementById('errorDiv');
            const errorText = document.getElementById('errorText');
            
            // UI state
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            text.textContent = 'Giriş Yapılıyor...';
            spinner.classList.remove('hidden');
            errorDiv.classList.add('hidden');
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.redirect) {
                    text.textContent = 'Yönlendiriliyor...';
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.message || 'Giriş yapılamadı.');
                }
            } catch (error) {
                // Restore UI state
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                text.textContent = 'Giriş Yap';
                spinner.classList.add('hidden');
                
                errorText.textContent = error.message || 'Giriş yapılırken bir hata oluştu.';
                errorDiv.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
