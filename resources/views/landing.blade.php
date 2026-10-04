<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pulsewatch — Uptime & Performance Monitor</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="max-w-3xl mx-auto px-6 py-24 text-center">
        <h1 class="text-5xl font-bold tracking-tight">Pulsewatch</h1>
        <p class="mt-4 text-xl text-gray-600">
            Monitor uptime & response time website kamu. Dapatkan alert saat down.
        </p>

        <div class="mt-10 flex gap-3 justify-center">
            <a href="{{ route('register') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                Get Started
            </a>
            <a href="{{ route('login') }}"
               class="px-6 py-3 rounded-lg font-medium border border-gray-300 hover:bg-gray-100">
                Login
            </a>
        </div>

        <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="font-semibold text-lg">⏱️ Real-time Monitoring</h3>
                <p class="text-gray-600 mt-2 text-sm">
                    Ping berkala ke endpoint kamu dengan interval yang bisa diatur.
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="font-semibold text-lg">📊 Visual Analytics</h3>
                <p class="text-gray-600 mt-2 text-sm">
                    Chart response time, uptime %, dan riwayat ping.
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="font-semibold text-lg">🔔 Instant Alerts</h3>
                <p class="text-gray-600 mt-2 text-sm">
                    Email otomatis ketika monitor terdeteksi down.
                </p>
            </div>
        </div>
    </div>
</body>
</html>