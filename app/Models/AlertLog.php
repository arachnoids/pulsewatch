<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitor_id',
        'type',
        'channel',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}