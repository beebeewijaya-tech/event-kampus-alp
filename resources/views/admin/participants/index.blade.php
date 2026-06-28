<x-layouts.admin>
    <h1 class="text-xl font-bold mb-1">Peserta: {{ $event->title }}</h1>
    <p class="text-sm text-gray-500 mb-4">{{ $event->event_date->format('d M Y') }}</p>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">{{ session('success') }}</div>
    @endif

    <div x-data="{ tab: 'confirmed' }">
        <div class="flex gap-4 border-b mb-4">
            <button @click="tab = 'confirmed'"
                :class="tab === 'confirmed' ? 'border-b-2 border-blue-500 text-blue-600 font-medium' : 'text-gray-500'"
                class="pb-2 text-sm">
                Terkonfirmasi ({{ count($confirmed) }})
            </button>
            <button @click="tab = 'waiting'"
                :class="tab === 'waiting' ? 'border-b-2 border-blue-500 text-blue-600 font-medium' : 'text-gray-500'"
                class="pb-2 text-sm">
                Waiting List ({{ count($waitingList) }})
            </button>
        </div>

        <div x-show="tab === 'confirmed'">
            <table class="w-full text-sm bg-white shadow rounded overflow-hidden">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Kategori</th>
                        <th class="px-4 py-2">Kode</th>
                        <th class="px-4 py-2">Check-in</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($confirmed as $reg)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $reg->user->name }}</td>
                            <td class="px-4 py-2">{{ $reg->user->email }}</td>
                            <td class="px-4 py-2">{{ $reg->eventCategory->name }}</td>
                            <td class="px-4 py-2 font-mono">{{ $reg->check_in_code }}</td>
                            <td class="px-4 py-2">
                                @if($reg->checked_in_at)
                                    <span class="text-green-600">{{ $reg->checked_in_at->format('d M H:i') }}</span>
                                @else
                                    <span class="text-gray-400">Belum</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                @if(!$reg->checked_in_at)
                                    <form method="POST" action="{{ route('admin.events.checkin', [$event, $reg]) }}">
                                        @csrf
                                        <button class="px-2 py-1 bg-blue-600 text-white rounded text-xs">Check-in</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.events.participants.destroy', [$event, $reg]) }}"
                                    onsubmit="return confirm('Hapus peserta ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-4 text-center text-gray-400">Tidak ada peserta.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div x-show="tab === 'waiting'">
            <table class="w-full text-sm bg-white shadow rounded overflow-hidden">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Kategori</th>
                        <th class="px-4 py-2">Tanggal Daftar</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waitingList as $reg)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $reg->user->name }}</td>
                            <td class="px-4 py-2">{{ $reg->user->email }}</td>
                            <td class="px-4 py-2">{{ $reg->eventCategory->name }}</td>
                            <td class="px-4 py-2">{{ $reg->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-2">
                                <form method="POST" action="{{ route('admin.events.participants.destroy', [$event, $reg]) }}"
                                    onsubmit="return confirm('Hapus dari waiting list?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-4 text-center text-gray-400">Tidak ada waiting list.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
