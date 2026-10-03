<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ping extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitor_id',
        'status_code',
        'response_time_ms',
        'status',
        'error_message',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}