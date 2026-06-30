<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Event</h1>
            <p class="text-sm text-gray-500">Isi data event baru yang akan ditampilkan kepada peserta.</p>
        </div>

        <a href="{{ route('admin.events.index') }}"
           class="px-4 py-2 text-sm rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <p class="font-semibold mb-2">Ada data yang perlu diperbaiki:</p>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Event</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Contoh: Seminar Karier Digital">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="5"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Tuliskan deskripsi singkat event">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Poster Image</label>
                <input type="text" name="poster_img" value="{{ old('poster_img') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Contoh: /images/poster-event.jpg">
                <p class="text-xs text-gray-500 mt-1">Untuk sementara isi dengan path/link gambar poster.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Event</label>
                    <input type="datetime-local" name="event_date" value="{{ old('event_date') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batas Registrasi</label>
                    <input type="datetime-local" name="registration_deadline" value="{{ old('registration_deadline') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="open" @selected(old('status') === 'open')>Open</option>
                    <option value="closed" @selected(old('status') === 'closed')>Closed</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.events.index') }}"
                   class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Simpan Event
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
