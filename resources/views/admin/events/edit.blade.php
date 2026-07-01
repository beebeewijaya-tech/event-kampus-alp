<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Event</h1>
            <p class="text-sm text-gray-500">Ubah data event yang sudah dibuat.</p>
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
        <form method="POST" action="{{ route('admin.events.update', $event) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Event</label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Contoh: Seminar Karier Digital">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="5"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Tuliskan deskripsi singkat event">{{ old('description', $event->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Poster Image</label>
                <input type="text" name="poster_img" value="{{ old('poster_img', $event->poster_img) }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Contoh: /images/poster-event.jpg">
                <p class="text-xs text-gray-500 mt-1">Untuk sementara isi dengan path/link gambar poster.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Event</label>
                    <input type="datetime-local" name="event_date"
                           value="{{ old('event_date', optional($event->event_date)->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batas Registrasi</label>
                    <input type="datetime-local" name="registration_deadline"
                           value="{{ old('registration_deadline', optional($event->registration_deadline)->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="open" @selected(old('status', $event->status) === 'open')>Open</option>
                    <option value="closed" @selected(old('status', $event->status) === 'closed')>Closed</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.events.index') }}"
                   class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Update Event
                </button>
            </div>
        </form>
    </div>
   <div class="mt-8 bg-white rounded-xl shadow p-6">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Kategori Event</h2>
            <p class="text-sm text-gray-500">
                Kelola kategori, kuota, dan harga untuk event ini.
            </p>
        </div>
    </div>

    @if($event->categories->isEmpty())
        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-gray-500">
            Belum ada kategori untuk event ini.
        </div>
    @else
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3">Nama</th>
                        <th class="text-left px-4 py-3">Kuota</th>
                        <th class="text-left px-4 py-3">Harga</th>
                        <th class="text-left px-4 py-3">Peserta</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($event->categories as $category)
                        <tr class="border-t">
                            <td class="px-4 py-3 font-medium">
                                {{ $category->name }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $category->quota }}
                            </td>

                            <td class="px-4 py-3">
                                @if($category->price == 0)
                                    Gratis
                                @else
                                    Rp {{ number_format($category->price,0,',','.') }}
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                {{ $category->registrations->count() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</x-layouts.admin>
