<?php

namespace App\Jobs;

use App\Models\WebsiteAudit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class WebCrawlerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $audit;

    public function __construct(WebsiteAudit $audit)
    {
        $this->audit = $audit;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->audit->update(['status' => 'crawling']);

        try {
            $pythonServiceUrl = env('PYTHON_SERVICE_URL', 'http://ai_service:8001');
            $internalApiKey = env('INTERNAL_API_KEY');

            $response = Http::withHeaders([
                'X-Internal-API-Key' => $internalApiKey
            ])->post($pythonServiceUrl . '/internal/audit', [
                'audit_id' => $this->audit->id,
                'url' => $this->audit->url
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->audit->update([
                    'status' => 'completed',
                    'seo_score' => $data['seo_score'] ?? null,
                    'security_score' => $data['security_score'] ?? null,
                    'performance_score' => $data['performance_score'] ?? null,
                    'accessibility_score' => $data['accessibility_score'] ?? null,
                    'total_score' => $data['total_score'] ?? null,
                    'report_data' => $data['report_data'] ?? null,
                    'ai_summary' => $data['ai_summary'] ?? null,
                    'cross_intelligence_report' => $data['cross_intelligence_report'] ?? null
                ]);
            } else {
                $this->audit->update([
                    'status' => 'failed',
                    'error_message' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            $this->audit->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
