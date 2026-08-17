<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebsiteAudit;

class WebsiteAuditController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->isAdmin()) {
            $websites = \App\Models\Website::with(['scans' => function($q) {
                $q->latest();
            }, 'user'])->latest()->get();
        } else {
            $websites = $user->websites()->with(['scans' => function($q) {
                $q->latest();
            }])->latest()->get();
        }
        return view('audit.index', compact('websites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $url = rtrim($request->url, '/');

        $website = \Illuminate\Support\Facades\Auth::user()->websites()->firstOrCreate([
            'url' => $url
        ]);

        $audit = $website->scans()->create([
            'url' => $url,
            'status' => 'pending'
        ]);

        \App\Jobs\WebCrawlerJob::dispatch($audit);

        return redirect()->route('audit.show', $website->id)->with('success', 'Website inceleme kuyruğa alındı. Sayfayı yenileyerek durumu takip edebilirsiniz.');
    }

    public function show($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = $user->isAdmin() ? \App\Models\Website::query() : $user->websites();
        
        $website = $query->with(['scans' => function($q) {
            $q->latest();
        }])->findOrFail($id);
        
        return view('audit.show', compact('website'));
    }

    public function destroy($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = $user->isAdmin() ? \App\Models\Website::query() : $user->websites();
        
        $website = $query->findOrFail($id);
        $website->delete();
        return redirect()->route('audit.index')->with('success', 'Website ve tüm geçmiş taramaları başarıyla silindi.');
    }
}
