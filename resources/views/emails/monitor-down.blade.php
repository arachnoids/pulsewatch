<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; padding: 20px;">
    <h2 style="color: #dc2626;">🔴 Monitor Down</h2>

    <p>Monitor <strong>{{ $monitor->name }}</strong> terdeteksi <strong>DOWN</strong>.</p>

    <table style="border-collapse: collapse; margin-top: 12px;">
        <tr>
            <td style="padding: 6px 12px 6px 0;"><strong>URL</strong></td>
            <td>{{ $monitor->url }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px 6px 0;"><strong>Status Code</strong></td>
            <td>{{ $ping->status_code ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px 6px 0;"><strong>Response Time</strong></td>
            <td>{{ $ping->response_time_ms }}ms</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px 6px 0;"><strong>Waktu</strong></td>
            <td>{{ $ping->checked_at->format('d M Y H:i:s') }}</td>
        </tr>
        @if ($ping->error_message)
        <tr>
            <td style="padding: 6px 12px 6px 0;"><strong>Error</strong></td>
            <td style="color: #dc2626;">{{ $ping->error_message }}</td>
        </tr>
        @endif
    </table>

    <p style="margin-top: 20px; color: #666; font-size: 12px;">
        Pesan otomatis dari Pulsewatch.
    </p>
</body>
</html>