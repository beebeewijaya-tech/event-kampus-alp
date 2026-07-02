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
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="5"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $event->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Poster Image</label>
                <input type="text" name="poster_img" value="{{ old('poster_img', $event->poster_img) }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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

            <div class="border-t pt-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Kategori Tiket</h2>
                        <p class="text-sm text-gray-500">Edit, tambah, atau hapus kategori tiket event ini.</p>
                    </div>

                    <button type="button" id="add-category"
                            class="px-3 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700">
                        + Tambah Kategori
                    </button>
                </div>

                <div id="categories-wrapper" class="space-y-4">
                    @forelse ($event->categories as $index => $category)
                        <div class="category-item border rounded-lg p-4 bg-gray-50">
                            <input type="hidden" name="categories[{{ $index }}][id]" value="{{ $category->id }}">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                                    <input type="text" name="categories[{{ $index }}][name]"
                                           value="{{ old("categories.$index.name", $category->name) }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kuota</label>
                                    <input type="number" name="categories[{{ $index }}][quota]" min="1"
                                           value="{{ old("categories.$index.quota", $category->quota) }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                                    <input type="number" name="categories[{{ $index }}][price]" min="0"
                                           value="{{ old("categories.$index.price", $category->price) }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kategori</label>
                                <textarea name="categories[{{ $index }}][description]" rows="2"
                                          class="w-full rounded-lg border-gray-300 shadow-sm">{{ old("categories.$index.description", $category->description) }}</textarea>
                            </div>

                            <button type="button"
                                    class="remove-category mt-3 text-sm text-red-600 hover:text-red-800">
                                Hapus Kategori
                            </button>
                        </div>
                    @empty
                        <div class="category-item border rounded-lg p-4 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                                    <input type="text" name="categories[0][name]"
                                           class="w-full rounded-lg border-gray-300 shadow-sm"
                                           placeholder="Regular / VIP / Mahasiswa">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kuota</label>
                                    <input type="number" name="categories[0][quota]" min="1"
                                           class="w-full rounded-lg border-gray-300 shadow-sm"
                                           placeholder="50">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                                    <input type="number" name="categories[0][price]" min="0"
                                           class="w-full rounded-lg border-gray-300 shadow-sm"
                                           placeholder="50000">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kategori</label>
                                <textarea name="categories[0][description]" rows="2"
                                          class="w-full rounded-lg border-gray-300 shadow-sm"
                                          placeholder="Opsional"></textarea>
                            </div>

                            <button type="button"
                                    class="remove-category mt-3 text-sm text-red-600 hover:text-red-800">
                                Hapus Kategori
                            </button>
                        </div>
                    @endforelse
                </div>
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

    <script>
        let categoryIndex = {{ max($event->categories->count(), 1) }};

        document.getElementById('add-category').addEventListener('click', function () {
            const wrapper = document.getElementById('categories-wrapper');

            const html = `
                <div class="category-item border rounded-lg p-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                            <input type="text" name="categories[${categoryIndex}][name]"
                                   class="w-full rounded-lg border-gray-300 shadow-sm"
                                   placeholder="Regular / VIP / Mahasiswa">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kuota</label>
                            <input type="number" name="categories[${categoryIndex}][quota]" min="1"
                                   class="w-full rounded-lg border-gray-300 shadow-sm"
                                   placeholder="50">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                            <input type="number" name="categories[${categoryIndex}][price]" min="0"
                                   class="w-full rounded-lg border-gray-300 shadow-sm"
                                   placeholder="50000">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kategori</label>
                        <textarea name="categories[${categoryIndex}][description]" rows="2"
                                  class="w-full rounded-lg border-gray-300 shadow-sm"
                                  placeholder="Opsional"></textarea>
                    </div>

                    <button type="button"
                            class="remove-category mt-3 text-sm text-red-600 hover:text-red-800">
                        Hapus Kategori
                    </button>
                </div>
            `;

            wrapper.insertAdjacentHTML('beforeend', html);
            categoryIndex++;
        });

        document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-category')) {
        const items = document.querySelectorAll('.category-item');

        if (items.length <= 1) {
            alert('Minimal harus ada satu kategori tiket.');
            return;
        }

        e.target.closest('.category-item').remove();
    }
});
    </script>
</x-layouts.admin>
