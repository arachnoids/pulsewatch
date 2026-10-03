<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\Ping;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PingMonitor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Monitor $monitor)
    {
    }

    public function handle(): void
    {
        $start = microtime(true);
        $status = 'down';
        $statusCode = null;
        $errorMessage = null;

        try {
            $response = Http::timeout($this->monitor->timeout_seconds)
                ->send($this->monitor->method, $this->monitor->url);

            $statusCode = $response->status();
            $status = $response->successful() ? 'up' : 'down';
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            $status = 'down';
        }

        $responseTime = (int) round((microtime(true) - $start) * 1000);

        // Kalau response lambat (> 3 detik), tandai degraded
        if ($status === 'up' && $responseTime > 3000) {
            $status = 'degraded';
        }

        Ping::create([
            'monitor_id' => $this->monitor->id,
            'status_code' => $statusCode,
            'response_time_ms' => $responseTime,
            'status' => $status,
            'error_message' => $errorMessage,
            'checked_at' => now(),
        ]);
    }
}