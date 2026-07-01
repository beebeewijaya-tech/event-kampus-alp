<x-layouts.app>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Pendaftaran</h1>
        <a href="{{ route('events.index') }}" class="text-blue-600 hover:underline text-sm">Lihat Events</a>
    </div>

    @if($registrations->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500">Belum ada pendaftaran.</p>
            <a href="{{ route('events.index') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                Temukan Event
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Event</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Kategori</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Status</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Kode Check-in</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrations as $registration)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-800 font-medium">
                                    {{ $registration->eventCategory?->event?->title ?? 'Event tidak tersedia' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $registration->eventCategory?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($registration->status === 'confirmed')
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Confirmed</span>
                                    @elseif($registration->status === 'waiting_list')
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Waiting List</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($registration->check_in_code)
    <code class="font-mono text-gray-800 bg-gray-100 px-2 py-0.5 rounded text-xs">
        {{ $registration->check_in_code }}
    </code>
@else
    <span class="text-gray-400 text-xs">Belum tersedia</span>
@endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $registration->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layouts.app>
