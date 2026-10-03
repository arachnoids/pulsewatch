<?php

namespace App\Console\Commands;

use App\Jobs\PingMonitor;
use App\Models\Monitor;
use Illuminate\Console\Command;

class DispatchPings extends Command
{
    protected $signature = 'monitors:dispatch';
    protected $description = 'Dispatch ping jobs untuk semua monitor yang aktif';

    public function handle(): void
    {
        $monitors = Monitor::where('is_active', true)->get();

        foreach ($monitors as $monitor) {
            PingMonitor::dispatch($monitor);
        }

        $this->info("Dispatched {$monitors->count()} ping jobs.");
    }
}