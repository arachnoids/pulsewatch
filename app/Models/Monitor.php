<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'url',
        'method',
        'interval_seconds',
        'timeout_seconds',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pings()
    {
        return $this->hasMany(Ping::class);
    }

    public function latestPing()
    {
        return $this->hasOne(Ping::class)->latestOfMany('checked_at');
    }

    public function uptimePercentage(int $hours = 24): float
    {
        $pings = $this->pings()
            ->where('checked_at', '>=', now()->subHours($hours))
            ->get();

        if ($pings->isEmpty()) {
            return 0;
        }

        $up = $pings->where('status', 'up')->count();
        return round(($up / $pings->count()) * 100, 2);
    }

    public function averageResponseTime(int $hours = 24): ?int
    {
        $avg = $this->pings()
            ->where('checked_at', '>=', now()->subHours($hours))
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms');

        return $avg ? (int) round($avg) : null;
    }

    public function pingsLast24Hours()
    {
        return $this->pings()
            ->where('checked_at', '>=', now()->subHours(24))
            ->orderBy('checked_at')
            ->get();
    }
    
    public function alertLogs()
    {
        return $this->hasMany(AlertLog::class);
    }
}