@extends('layouts.app')

@section('header', 'Doküman Yönetimi')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in-up">
    
    <!-- Premium Upload Section -->
    <div class="bg-white/70 backdrop-blur-lg rounded-3xl shadow-xl shadow-blue-900/5 border border-white/50 p-8 overflow-hidden relative">
        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-64 h-64 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        
        <h3 class="text-xl font-bold mb-6 text-slate-800 flex items-center relative z-10">
            <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Yeni Doküman Yükle
        </h3>
        
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="relative z-10">
            @csrf
            <label for="file-upload" class="border-2 border-dashed border-blue-200 rounded-2xl p-12 flex flex-col items-center justify-center text-center bg-white/50 hover:bg-blue-50/50 hover:border-blue-400 transition-all duration-300 cursor-pointer group shadow-sm hover:shadow-md">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-5 group-hover:-translate-y-2 transition-transform duration-300 shadow-sm">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                </div>
                <p class="text-slate-700 font-semibold text-lg mb-2 group-hover:text-blue-600 transition-colors">Dosyaları buraya sürükleyin veya göz atın</p>
                <p class="text-sm text-slate-400 bg-slate-100/50 px-4 py-1.5 rounded-full">PDF, DOCX, XLSX (Maks. 10MB)</p>
                
                <input type="file" name="document" class="hidden" accept=".pdf,.docx,.xlsx" id="file-upload" onchange="showUploadOverlay(); this.form.submit()">
            </label>
        </form>
    </div>

    <!-- Premium Documents List Section -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-200/50 border border-white/60 overflow-hidden relative z-10">
        <div class="px-8 py-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gradient-to-r from-slate-50/50 to-white gap-4">
            <h3 class="text-xl font-bold text-slate-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Yüklenen Dokümanlar
            </h3>
            
            <div class="relative w-full sm:w-auto flex items-center space-x-2">
                <div class="relative">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Dokümanlarda ara..." class="pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white w-full sm:w-72 transition-all shadow-inner">
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="docsTable">
                <thead>
                    <tr class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                        <th class="px-8 py-5 cursor-pointer hover:text-slate-600" onclick="sortTable(0)">Doküman Adı ↕</th>
                        <th class="px-6 py-5 cursor-pointer hover:text-slate-600" onclick="sortTable(1)">Tür ↕</th>
                        <th class="px-6 py-5 cursor-pointer hover:text-slate-600" onclick="sortTable(2)">Durum ↕</th>
                        <th class="px-6 py-5 cursor-pointer hover:text-slate-600" onclick="sortTable(3)">Tarih ↕</th>
                        <th class="px-8 py-5 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($documents ?? [] as $doc)
                        @php /** @var \App\Models\Document $doc */ @endphp
                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4 shadow-sm
                                    {{ str_ends_with(strtolower($doc->file_name ?? ''), 'pdf') ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-500' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 group-hover:text-indigo-700 transition-colors">{{ $doc->title ?? $doc->file_name }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ number_format(($doc->file_size ?? 0) / 1024 / 1024, 2) }} MB</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm font-medium text-slate-500 uppercase">{{ $doc->file_type ?? pathinfo($doc->file_name ?? '', PATHINFO_EXTENSION) }}</td>
                        <td class="px-6 py-5">
                            @if(($doc->status ?? '') == 'completed' || ($doc->status ?? '') == 'processed' || ($doc->status ?? '') == 'işlendi')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100/80 text-emerald-700 border border-emerald-200/50 shadow-sm">
                                    <span class="w-1.5 h-1.5 mr-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                    İşlendi
                                </span>
                            @elseif(($doc->status ?? '') == 'failed' || ($doc->status ?? '') == 'hata')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100/80 text-red-700 border border-red-200/50 shadow-sm">
                                    <span class="w-1.5 h-1.5 mr-2 bg-red-500 rounded-full"></span>
                                    Hata
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100/80 text-amber-700 border border-amber-200/50 shadow-sm">
                                    <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    İşleniyor
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-sm font-medium text-slate-500">{{ $doc->created_at ? $doc->created_at->format('d M Y') : '-' }}</td>
                        <td class="px-8 py-5 text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @php
                                    $msg = $doc->status == 'failed' ? 'Hata Detayı: ' . addslashes(strip_tags($doc->processing_error ?? 'Bilinmeyen hata.')) : ($doc->status == 'processed' ? 'Doküman başarıyla işlendi ve RAG veritabanına eklendi.' : 'Doküman şu an arka planda işleniyor...');
                                @endphp
                                <a href="#" onclick="alert('{{ $msg }}'); return false;" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all shadow-sm hover:shadow">
                                    Detay
                                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                                
                                <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Bu dokümanı ve tüm AI (RAG) verilerini silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-red-200 rounded-lg text-red-500 hover:bg-red-50 hover:border-red-300 hover:text-red-600 transition-all shadow-sm hover:shadow">
                                        Sil
                                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <p class="text-slate-500 font-medium">Henüz yüklenmiş bir doküman bulunmuyor.</p>
                            <p class="text-slate-400 text-sm mt-1">Yukarıdaki alana sürükleyerek ilk dosyanızı yükleyebilirsiniz.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("docsTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            let txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("docsTable");
  switching = true;
  dir = "asc"; 
  while (switching) {
    switching = false;
    rows = table.rows;
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount ++;
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}

function showUploadOverlay() {
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center';
    overlay.innerHTML = `
        <div class="bg-white rounded-3xl p-10 flex flex-col items-center justify-center shadow-2xl max-w-sm w-full mx-4 transform transition-all">
            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 mb-6 relative">
                <svg class="w-10 h-10 animate-spin absolute" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Doküman Yükleniyor</h3>
            <p class="text-slate-500 text-center font-medium text-sm">Dosyanız sunucuya aktarılıyor, lütfen bekleyin...</p>
        </div>
    `;
    document.body.appendChild(overlay);
}
</script>
@endsection
