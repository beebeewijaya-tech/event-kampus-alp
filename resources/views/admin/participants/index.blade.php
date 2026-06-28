<x-layouts.admin>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Peserta: {{ $event->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $event->event_date->format('d M Y') }}</p>
        </div>
        <a href="{{ route('admin.events.index') }}"
           class="text-sm text-blue-600 hover:underline">← Kembali ke Daftar Event</a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div x-data="{ tab: 'confirmed' }">
        {{-- Tabs --}}
        <div class="flex gap-4 border-b border-gray-200 mb-6">
            <button
                @click="tab = 'confirmed'"
                :class="tab === 'confirmed' ? 'border-b-2 border-blue-500 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="pb-3 px-1 text-sm transition-colors">
                Peserta Terkonfirmasi ({{ count($confirmed) }})
            </button>
            <button
                @click="tab = 'waiting'"
                :class="tab === 'waiting' ? 'border-b-2 border-blue-500 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="pb-3 px-1 text-sm transition-colors">
                Waiting List ({{ count($waitingList) }})
            </button>
        </div>

        {{-- Confirmed Tab --}}
        <div x-show="tab === 'confirmed'">
            <div class="bg-white rounded-xl shadow overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Nama</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Kategori</th>
                            <th class="px-6 py-3 text-left">Kode Check-in</th>
                            <th class="px-6 py-3 text-left">Status Check-in</th>
                            <th class="px-6 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($confirmed as $reg)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $reg->user->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $reg->user->email }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $reg->eventCategory->name }}</td>
                                <td class="px-6 py-4 font-mono text-gray-700">{{ $reg->check_in_code }}</td>
                                <td class="px-6 py-4">
                                    @if($reg->isCheckedIn())
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Sudah Check-in ({{ $reg->checked_in_at->format('d M Y H:i') }})
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                            Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @unless($reg->isCheckedIn())
                                            <form method="POST" action="{{ route('admin.events.checkin', [$event, $reg]) }}">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                                    Check-in
                                                </button>
                                            </form>
                                        @endunless
                                        <form method="POST" action="{{ route('admin.events.participants.destroy', [$event, $reg]) }}"
                                              onsubmit="return confirm('Hapus peserta ini? Peserta waiting list berikutnya akan otomatis dikonfirmasi.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada peserta terkonfirmasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Waiting List Tab --}}
        <div x-show="tab === 'waiting'">
            <div class="bg-white rounded-xl shadow overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Nama</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Kategori</th>
                            <th class="px-6 py-3 text-left">Tanggal Daftar</th>
                            <th class="px-6 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($waitingList as $reg)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $reg->user->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $reg->user->email }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $reg->eventCategory->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $reg->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.events.participants.destroy', [$event, $reg]) }}"
                                          onsubmit="return confirm('Hapus peserta dari waiting list ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Tidak ada peserta di waiting list.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
