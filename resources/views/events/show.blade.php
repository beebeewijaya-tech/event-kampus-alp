<x-layouts.app>
    <div class="mb-4">
        <a href="{{ route('events.index') }}" class="text-blue-600 hover:underline text-sm">&larr; Kembali ke Daftar Event</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        {{-- Poster --}}
        @if($event->poster_img)
            <img src="{{ Storage::url($event->poster_img) }}" alt="{{ $event->title }}" class="w-full max-h-80 object-cover">
        @else
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                <span class="text-gray-400">Tidak ada poster</span>
            </div>
        @endif

        <div class="p-6">
            {{-- Title & Status --}}
            <div class="flex items-center gap-3 mb-4">
                <h1 class="text-2xl font-bold text-gray-800">{{ $event->title }}</h1>
                @if($event->status === 'open')
                    <span class="inline-block px-3 py-1 text-sm font-medium bg-green-100 text-green-700 rounded-full">Buka</span>
                @else
                    <span class="inline-block px-3 py-1 text-sm font-medium bg-red-100 text-red-700 rounded-full">Tutup</span>
                @endif
            </div>

            {{-- Description --}}
            <p class="text-gray-600 mb-6 leading-relaxed">{{ $event->description }}</p>

            {{-- Event Info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8 text-sm">
                <div>
                    <span class="font-medium text-gray-700">Tanggal Event:</span>
                    <span class="text-gray-600 ml-1">{{ $event->event_date->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Batas Pendaftaran:</span>
                    <span class="text-gray-600 ml-1">{{ $event->registration_deadline->format('d M Y') }}</span>
                </div>
            </div>

            {{-- Categories Table --}}
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Kategori Tiket</h2>

            @if($event->categories->isEmpty())
                <p class="text-gray-500 text-sm">Belum ada kategori tersedia.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left px-4 py-3 font-medium text-gray-700">Nama</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-700">Kuota</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-700">Sisa Slot</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-700">Harga</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($event->categories as $category)
                                @php
    $confirmedCount = $category->confirmed_count ?? 0;
    $isFull = $confirmedCount >= $category->quota;
    $availableSlots = max(0, $category->quota - $confirmedCount);
@endphp
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-800 font-medium">{{ $category->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $category->quota }}</td>
                                    <td class="px-4 py-3">
                                        @if($isFull)
                                            <span class="inline-block px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Waiting List</span>
                                        @else
                                            <span class="text-gray-600">{{ $availableSlots }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        @if($category->price == 0)
                                            Gratis
                                        @else
                                            Rp {{ number_format($category->price, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($event->status === 'open')
                                            @auth
                                                <a href="{{ route('registrations.create', ['event' => $event, 'category' => $category->id]) }}"
                                                   class="inline-block px-4 py-1.5 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                    {{ $isFull ? 'Daftar Waiting List' : 'Daftar' }}
                                                </a>
                                            @else
                                                <a href="{{ route('login') }}" class="inline-block px-4 py-1.5 text-sm font-medium bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                                    Login untuk Daftar
                                                </a>
                                            @endauth
                                        @else
                                            <span class="text-gray-400 text-sm">Pendaftaran Ditutup</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
