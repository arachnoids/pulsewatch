<?php

namespace App\Mail;

use App\Models\Monitor;
use App\Models\Ping;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonitorDownAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Monitor $monitor,
        public Ping $ping,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔴 {$this->monitor->name} is DOWN",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.monitor-down',
        );
    }
}