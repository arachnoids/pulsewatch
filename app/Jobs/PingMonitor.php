<?php

namespace App\Jobs;

use App\Mail\MonitorDownAlert;
use App\Models\AlertLog;
use App\Models\Monitor;
use App\Models\Ping;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class PingMonitor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Monitor $monitor)
    {
    }

    public function handle(): void
    {
        // Ambil status sebelumnya SEBELUM ping baru
        $previousPing = $this->monitor->latestPing;
        $previousStatus = $previousPing?->status;

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

        if ($status === 'up' && $responseTime > 3000) {
            $status = 'degraded';
        }

        $ping = Ping::create([
            'monitor_id' => $this->monitor->id,
            'status_code' => $statusCode,
            'response_time_ms' => $responseTime,
            'status' => $status,
            'error_message' => $errorMessage,
            'checked_at' => now(),
        ]);

        // Deteksi transisi status
        $this->handleStatusTransition($previousStatus, $status, $ping);
    }

    protected function handleStatusTransition(?string $previous, string $current, Ping $ping): void
    {
        // UP -> DOWN
        if ($previous === 'up' && $current === 'down') {
            $this->sendDownAlert($ping);
        }

        // DOWN -> UP (recovery)
        if ($previous === 'down' && $current === 'up') {
            AlertLog::create([
                'monitor_id' => $this->monitor->id,
                'type' => 'up',
                'channel' => 'email',
                'sent_at' => now(),
            ]);
        }
    }

    protected function sendDownAlert(Ping $ping): void
    {
        try {
            Mail::to($this->monitor->user->email)
                ->send(new MonitorDownAlert($this->monitor, $ping));

            AlertLog::create([
                'monitor_id' => $this->monitor->id,
                'type' => 'down',
                'channel' => 'email',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Log error tapi jangan crash worker
            logger()->error('Failed to send down alert: ' . $e->getMessage());
        }
    }
}