<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $monitor->name }}
            </h2>
            <a href="{{ route('monitors.index') }}"
               class="text-sm text-blue-600 hover:underline">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Info Monitor --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">URL</dt><dd class="font-medium">{{ $monitor->url }}</dd></div>
                    <div><dt class="text-gray-500">Method</dt><dd class="font-medium">{{ $monitor->method }}</dd></div>
                    <div><dt class="text-gray-500">Interval</dt><dd class="font-medium">{{ $monitor->interval_seconds }}s</dd></div>
                    <div><dt class="text-gray-500">Timeout</dt><dd class="font-medium">{{ $monitor->timeout_seconds }}s</dd></div>
                    <div>
                        <dt class="text-gray-500">Status Saat Ini</dt>
                        <dd>
                            @if ($monitor->latestPing)
                                @include('monitors._status-badge', ['status' => $monitor->latestPing->status])
                                <span class="text-gray-500 text-xs ml-1">
                                    {{ $monitor->latestPing->response_time_ms }}ms
                                    ({{ $monitor->latestPing->checked_at->diffForHumans() }})
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">Belum ada ping</span>
                            @endif
                        </dd>
                    </div>
                    <div><dt class="text-gray-500">Aktif</dt><dd class="font-medium">{{ $monitor->is_active ? 'Ya' : 'Tidak' }}</dd></div>
                </dl>
            </div>

            {{-- Statistik --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Uptime (24 jam)</div>
                    <div class="text-3xl font-bold mt-1">{{ $uptime }}%</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Avg Response (24 jam)</div>
                    <div class="text-3xl font-bold mt-1">
                        {{ $avgResponse ? $avgResponse . 'ms' : '-' }}
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Ping (24 jam)</div>
                    <div class="text-3xl font-bold mt-1">{{ $pings->count() }}</div>
                </div>
            </div>

            {{-- Chart --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Response Time (24 jam terakhir)</h3>
                @if ($pings->isEmpty())
                    <p class="text-gray-400 text-sm">Belum ada data ping.</p>
                @else
                    <canvas id="responseChart" height="80"></canvas>
                @endif
            </div>

            {{-- Riwayat Ping --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Riwayat Ping (10 terakhir)</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2">Waktu</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Code</th>
                            <th class="py-2">Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($monitor->pings()->latest('checked_at')->limit(10)->get() as $ping)
                            <tr class="border-b">
                                <td class="py-2">{{ $ping->checked_at->format('d M H:i:s') }}</td>
                                <td class="py-2">
                                    @include('monitors._status-badge', ['status' => $ping->status])
                                </td>
                                <td class="py-2">{{ $ping->status_code ?? '-' }}</td>
                                <td class="py-2">{{ $ping->response_time_ms }}ms</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-gray-400 text-center">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    @if (!$pings->isEmpty())
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const canvas = document.getElementById('responseChart');
                if (!canvas) return;

                const labels = @json($pings->pluck('checked_at')->map(fn($d) => $d->format('H:i')));
                const data = @json($pings->pluck('response_time_ms'));
                const colors = @json($pings->pluck('status')->map(fn($s) => $s === 'up' ? '#16a34a' : ($s === 'down' ? '#dc2626' : '#eab308')));

                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Response Time (ms)',
                            data: data,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: colors,
                            pointRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, title: { display: true, text: 'ms' } } },
                    },
                });
            });
        </script>
        @endpush
    @endif
</x-app-layout>