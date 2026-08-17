<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use App\Jobs\DocumentProcessingJob;

class DocumentController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->isAdmin()) {
            $documents = Document::orderBy('created_at', 'desc')->get();
        } else {
            $documents = $user->documents()->orderBy('created_at', 'desc')->get();
        }
        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,docx,xlsx|max:10240',
        ]);

        $file = $request->file('document');
        $originalName = $file->getClientOriginalName();
        $filename = time() . '_' . $originalName;
        $fileSize = $file->getSize();

        // Store file in public disk
        $path = $file->storeAs('documents', $filename, 'public');

        // Save to Database
        $document = Document::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'title' => $originalName,
            'file_name' => $filename,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $fileSize,
            'status' => 'pending',
        ]);

        // Dispatch Job to AI Service
        DocumentProcessingJob::dispatch($document);

        return redirect()->back()->with('success', 'Doküman başarıyla yüklendi ve işlenmek üzere kuyruğa alındı.');
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Delete file from storage
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Delete associated chunks from the shared database
        \Illuminate\Support\Facades\DB::table('document_chunks')->where('document_id', $document->id)->delete();

        // Delete the document record
        $document->delete();

        return redirect()->back()->with('success', 'Doküman ve ilgili RAG verileri başarıyla silindi.');
    }
}
