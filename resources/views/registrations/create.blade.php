<x-layouts.app>
    <div class="mb-4">
        <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:underline text-sm">&larr; Kembali ke Detail Event</a>
    </div>

    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Daftar Event: {{ $event->title }}</h1>

        <form action="{{ route('registrations.store', $event) }}" method="POST">
            @csrf

            {{-- Peserta Info (read-only display) --}}
            <div class="mb-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Informasi Pendaftar</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="block text-sm font-medium text-gray-600 mb-1">Nama</span>
                        <p class="text-gray-800 bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm">{{ $user->name }}</p>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-600 mb-1">Email</span>
                        <p class="text-gray-800 bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm">{{ $user->email }}</p>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-600 mb-1">No. Telepon</span>
                        <p class="text-gray-800 bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm">{{ $user->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Category Selection --}}
            <div class="mb-6">
                <h2 class="text-base font-semibold text-gray-700 mb-3">Pilih Kategori</h2>

                @error('event_category_id')
                    <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
                @enderror

                <div class="space-y-3">
                    @foreach($categories as $category)
                        @php
                            $confirmedCount = $category->registrations()->where('status', 'confirmed')->count();
                            $isFull = $confirmedCount >= $category->quota;
                            $isSelected = $selectedCategoryId == $category->id;
                        @endphp
                        <label class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer hover:bg-blue-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input
                                type="radio"
                                name="event_category_id"
                                value="{{ $category->id }}"
                                class="mt-1"
                                {{ $isSelected ? 'checked' : '' }}
                            >
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-800">{{ $category->name }}</span>
                                    <span class="text-sm text-gray-600">
                                        @if($category->price == 0)
                                            Gratis
                                        @else
                                            Rp {{ number_format($category->price, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="mt-1 text-sm">
                                    @if($isFull)
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Waiting List</span>
                                    @else
                                        <span class="text-gray-500">Sisa: {{ $category->quota - $confirmedCount }} kursi</span>
                                    @endif
                                </div>
                                @if($category->description)
                                    <p class="mt-1 text-sm text-gray-500">{{ $category->description }}</p>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                Daftar Sekarang
            </button>
        </form>
    </div>
</x-layouts.app>
