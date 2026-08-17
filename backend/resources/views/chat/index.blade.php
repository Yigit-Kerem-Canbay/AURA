@extends('layouts.app')

@section('header', 'AURA AI Asistan')

@section('content')
<div class="max-w-7xl mx-auto h-[82vh] flex bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200/60 font-sans">
    
    <!-- Sidebar (Dark Theme) -->
    <div class="w-72 bg-slate-900 flex flex-col flex-shrink-0">
        <div class="p-4 border-b border-slate-800">
            <a href="{{ route('chat.index') }}" class="flex items-center justify-between w-full px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-colors font-medium text-sm group">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-400 group-hover:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Yeni Sohbet
                </span>
            </a>
        </div>
        
        <div class="flex-1 overflow-y-auto p-3 space-y-1 scroll-smooth custom-scrollbar-dark">
            <h3 class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Geçmiş Sohbetler</h3>
            
            @forelse($conversations as $conv)
                <div class="relative group flex items-center rounded-lg transition-colors {{ isset($conversation) && $conversation->id == $conv->id ? 'bg-indigo-600/20' : 'hover:bg-white/5' }}">
                    <a href="{{ route('chat.show', $conv->id) }}" class="flex-1 flex flex-col px-3 py-2.5 {{ isset($conversation) && $conversation->id == $conv->id ? 'text-white' : 'text-slate-300' }}">
                        <span class="text-sm font-medium truncate pr-6">{{ $conv->title ?? 'Sohbet' }}</span>
                        <span class="text-[10px] text-slate-500 mt-1">{{ $conv->updated_at->diffForHumans() }}</span>
                    </a>
                    <form action="{{ route('chat.destroy', $conv->id) }}" method="POST" class="absolute right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-white/10 rounded-md transition-colors" title="Sohbeti Sil" onclick="return confirm('Bu sohbet geçmişini silmek istediğinize emin misiniz?');">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            @empty
                <div class="px-3 py-4 text-center">
                    <p class="text-xs text-slate-500">Henüz geçmiş sohbetiniz yok.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col bg-white relative">
        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto px-4 py-8 sm:px-8 space-y-8 scroll-smooth custom-scrollbar" id="chat-messages">
            @if(isset($conversation) && $conversation->messages->count() > 0)
                @foreach($conversation->messages as $msg)
                    @if($msg->role == 'user')
                        <!-- User Message -->
                        <div class="flex flex-row-reverse items-start group max-w-4xl mx-auto w-full">
                            <div class="flex-shrink-0 ml-4">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                </div>
                            </div>
                            <div class="bg-slate-100 text-slate-800 px-5 py-3.5 rounded-2xl rounded-tr-sm max-w-2xl text-[15px] leading-relaxed">
                                {{ $msg->content }}
                            </div>
                        </div>
                    @else
                        <!-- AI Message -->
                        <div class="flex items-start group max-w-4xl mx-auto w-full">
                            <div class="flex-shrink-0 mr-4 mt-1">
                                <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center text-white shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                            </div>
                            <div class="flex-1 max-w-2xl text-[15px] leading-relaxed text-slate-700">
                                <div class="prose prose-slate prose-sm max-w-none">
                                    {!! nl2br(e($msg->content)) !!}
                                </div>
                                
                                @if(is_array($msg->sources) && count($msg->sources) > 0)
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach($msg->sources as $src)
                                            <div class="inline-flex items-center px-2.5 py-1 rounded-md bg-indigo-50 border border-indigo-100 text-[11px] text-indigo-700 font-medium cursor-help" title="Benzerlik: {{ number_format($src['similarity'] ?? 0, 3) }}">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                {{ $src['title'] ?? 'Doküman' }} (Syf: {{ $src['page_number'] ?? '?' }})
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <!-- Empty State -->
                <div class="h-full flex flex-col items-center justify-center text-center max-w-xl mx-auto px-4 mt-12">
                    <div class="w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center text-white mb-6 shadow-xl shadow-slate-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 mb-2">Nasıl yardımcı olabilirim?</h2>
                    <p class="text-slate-500 text-sm">AURA, yüklediğiniz denetim raporları ve kurum dokümanları üzerinde akıllı aramalar yaparak size anında, güvenilir yanıtlar sunar.</p>
                </div>
            @endif
        </div>

        <!-- Input Box -->
        <div class="bg-white px-4 sm:px-8 pb-6 pt-2">
            <div class="max-w-4xl mx-auto relative">
                <form id="chat-form" class="relative flex items-end shadow-sm border border-slate-300 rounded-2xl bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-400 transition-all overflow-hidden">
                    <textarea 
                        id="chat-input" 
                        rows="1"
                        class="w-full py-4 pl-5 pr-16 bg-transparent border-none focus:ring-0 resize-none text-[15px] max-h-32 text-slate-700" 
                        placeholder="AURA'ya soru sorun..."
                        style="min-height: 56px;"
                    ></textarea>
                    
                    <button type="submit" id="submit-btn" class="absolute right-3 bottom-2.5 p-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
                <div class="text-center mt-2">
                    <span class="text-[11px] text-slate-400">Yapay zeka hatalı sonuçlar üretebilir. Önemli verileri lütfen doğrulayın.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let currentConversationId = {{ isset($conversation) ? $conversation->id : 'null' }};
    const messagesDiv = document.getElementById('chat-messages');
    const input = document.getElementById('chat-input');
    const submitBtn = document.getElementById('submit-btn');

    // Auto-resize textarea
    input.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight < 128 ? this.scrollHeight : 128) + 'px';
    });

    // Enter to submit (Shift+Enter for new line)
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('chat-form').dispatchEvent(new Event('submit'));
        }
    });

    // Scroll to bottom
    const scrollToBottom = () => {
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    };
    scrollToBottom();

    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const message = input.value.trim();
        if (!message) return;

        // User Message HTML
        messagesDiv.innerHTML += `
            <div class="flex flex-row-reverse items-start group max-w-4xl mx-auto w-full animate-fade-in-up">
                <div class="flex-shrink-0 ml-4">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-xs">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                </div>
                <div class="bg-slate-100 text-slate-800 px-5 py-3.5 rounded-2xl rounded-tr-sm max-w-2xl text-[15px] leading-relaxed">
                    ${message}
                </div>
            </div>
        `;
        
        input.value = '';
        input.style.height = 'auto';
        input.disabled = true;
        submitBtn.disabled = true;
        scrollToBottom();

        // Loading HTML
        const loadingId = 'loading-' + Date.now();
        messagesDiv.innerHTML += `
            <div class="flex items-start group max-w-4xl mx-auto w-full animate-fade-in-up" id="${loadingId}">
                <div class="flex-shrink-0 mr-4 mt-1">
                    <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center text-white shadow-sm">
                        <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <div class="bg-white border border-slate-100 shadow-sm rounded-2xl rounded-tl-sm px-5 py-4 max-w-2xl flex items-center space-x-1.5">
                    <div class="w-2 h-2 bg-slate-300 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-slate-300 rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
                    <div class="w-2 h-2 bg-slate-300 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                </div>
            </div>
        `;
        scrollToBottom();

        // Fetch
        fetch('/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ 
                query: message,
                conversation_id: currentConversationId
            })
        })
        .then(res => {
            if(!res.ok) throw new Error('Sunucu Hatası: ' + res.status);
            return res.json();
        })
        .then(data => {
            document.getElementById(loadingId).remove();
            
            if (data.message && !data.answer) {
                throw new Error(data.message); // Handle Laravel exceptions if they bleed through
            }

            if (!currentConversationId && data.conversation_id) {
                currentConversationId = data.conversation_id;
                window.history.pushState({}, '', '/chat/' + currentConversationId);
            }
            
            let responseText = data.answer || data.error || 'Cevap üretilemedi.';
            // We won't use replace for line breaks immediately to preserve tags if any, but we will type it out.
            
            let sourcesHtml = '';
            if (data.sources && data.sources.length > 0) {
                sourcesHtml = '<div class="mt-4 flex flex-wrap gap-2 opacity-0 transition-opacity duration-1000" id="sources-' + Date.now() + '">';
                data.sources.forEach(src => {
                    sourcesHtml += `
                        <div class="inline-flex items-center px-2.5 py-1 rounded-md bg-indigo-50 border border-indigo-100 text-[11px] text-indigo-700 font-medium cursor-help" title="Benzerlik: ${parseFloat(src.similarity).toFixed(3)}">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            ${src.title} (Syf: ${src.page_number})
                        </div>`;
                });
                sourcesHtml += '</div>';
            }
            
            const msgId = 'msg-' + Date.now();
            const sourcesId = 'sources-' + Date.now(); // We reuse the id to fade it in later

            messagesDiv.innerHTML += `
                <div class="flex items-start group max-w-4xl mx-auto w-full animate-fade-in-up">
                    <div class="flex-shrink-0 mr-4 mt-1">
                        <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center text-white shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                    </div>
                    <div class="flex-1 max-w-2xl text-[15px] leading-relaxed text-slate-700">
                        <div class="prose prose-slate prose-sm max-w-none" id="${msgId}">
                            <span class="inline-block w-1.5 h-4 bg-slate-400 animate-pulse"></span>
                        </div>
                        ${sourcesHtml.replace('sources-', 'sources-' + msgId)}
                    </div>
                </div>
            `;
            scrollToBottom();

            // Typewriter effect
            const msgContainer = document.getElementById(msgId);
            let i = 0;
            // Handle newlines properly
            const textToType = responseText;
            
            function typeWriter() {
                if (i < textToType.length) {
                    let char = textToType.charAt(i);
                    // Check if it's a newline
                    if (char === '\\n') {
                        msgContainer.innerHTML = msgContainer.innerHTML.replace('<span class="inline-block w-1.5 h-4 bg-slate-400 animate-pulse"></span>', '<br><span class="inline-block w-1.5 h-4 bg-slate-400 animate-pulse"></span>');
                    } else {
                        // Append char before cursor
                        msgContainer.innerHTML = msgContainer.innerHTML.replace('<span class="inline-block w-1.5 h-4 bg-slate-400 animate-pulse"></span>', char + '<span class="inline-block w-1.5 h-4 bg-slate-400 animate-pulse"></span>');
                    }
                    i++;
                    if (i % 3 === 0) scrollToBottom(); // Scroll periodically
                    setTimeout(typeWriter, 10); // Very fast typing
                } else {
                    // Remove cursor
                    msgContainer.innerHTML = msgContainer.innerHTML.replace('<span class="inline-block w-1.5 h-4 bg-slate-400 animate-pulse"></span>', '');
                    // Fade in sources
                    const srcDiv = document.getElementById('sources-' + msgId);
                    if (srcDiv) {
                        srcDiv.classList.remove('opacity-0');
                    }
                    scrollToBottom();
                }
            }
            typeWriter();
        })
        .catch(err => {
            document.getElementById(loadingId)?.remove();
            messagesDiv.innerHTML += `
                <div class="flex items-start group max-w-4xl mx-auto w-full animate-fade-in-up">
                    <div class="flex-shrink-0 mr-4 mt-1">
                        <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center text-white shadow-sm">
                            !
                        </div>
                    </div>
                    <div class="flex-1 max-w-2xl text-[15px] leading-relaxed text-red-600 bg-red-50 p-4 rounded-xl border border-red-100">
                        <strong>Hata:</strong> ${err.message}
                    </div>
                </div>
            `;
            scrollToBottom();
        })
        .finally(() => {
            input.disabled = false;
            submitBtn.disabled = false;
            input.focus();
        });
    });
</script>

<style>
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.3s ease-out forwards;
    }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .custom-scrollbar-dark::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar-dark::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar-dark::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .custom-scrollbar-dark::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>
@endsection
