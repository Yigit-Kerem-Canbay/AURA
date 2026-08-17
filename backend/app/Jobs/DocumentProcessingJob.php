<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DocumentProcessingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $document;

    /**
     * Create a new job instance.
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->document->update(['status' => 'processing']);

        try {
            $pythonServiceUrl = env('PYTHON_SERVICE_URL', 'http://ai_service:8001');
            $internalApiKey = env('INTERNAL_API_KEY');

            $response = Http::withHeaders([
                'X-Internal-API-Key' => $internalApiKey
            ])->post($pythonServiceUrl . '/internal/documents/process', [
                'document_id' => $this->document->id,
                'file_path' => storage_path('app/public/' . $this->document->file_path)
            ]);

            if ($response->successful()) {
                $this->document->update(['status' => 'processed']);
            } else {
                $this->document->update([
                    'status' => 'failed',
                    'processing_error' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            $this->document->update([
                'status' => 'failed',
                'processing_error' => $e->getMessage()
            ]);
        }
    }
}
