<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Monitors
            </h2>
            <a href="{{ route('monitors.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                + Tambah Monitor
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($monitors->isEmpty())
                    <p class="text-gray-500">Belum ada monitor. Klik "Tambah Monitor" untuk mulai.</p>
                @else
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Nama</th>
                                <th class="py-2">URL</th>
                                <th class="py-2">Interval</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monitors as $monitor)
                                <tr class="border-b">
                                    <td class="py-2">{{ $monitor->name }}</td>
                                    <td class="py-2">{{ $monitor->url }}</td>
                                    <td class="py-2">{{ $monitor->interval_seconds }}s</td>
                                    <td class="py-2">
                                        @if ($monitor->latestPing)
                                            @include('monitors._status-badge', ['status' => $monitor->latestPing->status])
                                            <span class="text-gray-500 text-xs ml-1">
                                                {{ $monitor->latestPing->response_time_ms }}ms
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-sm">Belum ada ping</span>
                                        @endif
                                    </td>
                                    <td class="py-2">
                                        <a href="{{ route('monitors.show', $monitor) }}"
                                            class="text-blue-600 hover:underline">Lihat</a>
                                        <a href="{{ route('monitors.edit', $monitor) }}"
                                            class="text-yellow-600 hover:underline ml-2">Edit</a>
                                        <form action="{{ route('monitors.destroy', $monitor) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('Hapus monitor ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ml-2">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>