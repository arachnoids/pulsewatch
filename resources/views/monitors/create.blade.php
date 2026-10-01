<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Monitor
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('monitors.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-1">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full border rounded p-2" required>
                        @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">URL</label>
                        <input type="url" name="url" value="{{ old('url') }}"
                               placeholder="https://example.com"
                               class="w-full border rounded p-2" required>
                        @error('url') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Interval (detik)</label>
                        <input type="number" name="interval_seconds"
                               value="{{ old('interval_seconds', 60) }}"
                               min="30" max="3600"
                               class="w-full border rounded p-2" required>
                        @error('interval_seconds') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Timeout (detik)</label>
                        <input type="number" name="timeout_seconds"
                               value="{{ old('timeout_seconds', 10) }}"
                               min="1" max="60"
                               class="w-full border rounded p-2" required>
                        @error('timeout_seconds') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Simpan
                        </button>
                        <a href="{{ route('monitors.index') }}"
                           class="px-4 py-2 rounded border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>