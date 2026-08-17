<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Auth::user()->conversations()->orderBy('updated_at', 'desc')->get();
        return view('chat.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = Auth::user()->conversations()->with('messages')->findOrFail($id);
        $conversations = Auth::user()->conversations()->orderBy('updated_at', 'desc')->get();
        return view('chat.index', compact('conversations', 'conversation'));
    }

    public function destroy($id)
    {
        $conversation = Auth::user()->conversations()->findOrFail($id);
        $conversation->delete();
        return redirect()->route('chat.index');
    }

    public function ask(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
            'conversation_id' => 'nullable|exists:conversations,id',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'integer'
        ]);

        $queryText = $request->input('query');
        $conversationId = $request->input('conversation_id');

        if (!$conversationId) {
            $conversation = Auth::user()->conversations()->create([
                'title' => mb_substr($queryText, 0, 30) . (mb_strlen($queryText) > 30 ? '...' : '')
            ]);
            $conversationId = $conversation->id;
        } else {
            $conversation = Auth::user()->conversations()->findOrFail($conversationId);
            $conversation->touch(); // Update updated_at
        }

        // Save user message
        $conversation->messages()->create([
            'role' => 'user',
            'content' => $queryText
        ]);

        try {
            $pythonServiceUrl = env('PYTHON_SERVICE_URL', 'http://ai_service:8001');
            $internalApiKey = env('INTERNAL_API_KEY');

            $response = Http::withHeaders([
                'X-Internal-API-Key' => $internalApiKey
            ])->post($pythonServiceUrl . '/internal/chat', [
                'query' => $queryText,
                'document_ids' => $request->input('document_ids', [])
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Save AI message
                $conversation->messages()->create([
                    'role' => 'assistant',
                    'content' => $data['answer'] ?? '',
                    'sources' => $data['sources'] ?? [],
                    'prompt_tokens' => $data['usage']['prompt_tokens'] ?? null,
                    'completion_tokens' => $data['usage']['completion_tokens'] ?? null,
                ]);

                // Append conversation_id to response
                $data['conversation_id'] = $conversationId;

                return response()->json($data);
            }

            return response()->json(['error' => 'AI Service failed to respond', 'details' => $response->body()], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
