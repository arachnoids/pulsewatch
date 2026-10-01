<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $monitor->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="space-y-2">
                    <div><strong>URL:</strong> {{ $monitor->url }}</div>
                    <div><strong>Method:</strong> {{ $monitor->method }}</div>
                    <div><strong>Interval:</strong> {{ $monitor->interval_seconds }} detik</div>
                    <div><strong>Timeout:</strong> {{ $monitor->timeout_seconds }} detik</div>
                    <div><strong>Status:</strong>
                        {{ $monitor->is_active ? 'Aktif' : 'Nonaktif' }}
                    </div>
                </dl>

                <a href="{{ route('monitors.index') }}"
                   class="inline-block mt-4 text-blue-600 hover:underline">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>