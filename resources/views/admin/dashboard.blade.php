<x-layouts.admin>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Total Event</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalEvents }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Total Pendaftaran</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalRegistrations }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Terkonfirmasi</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">{{ $confirmedRegistrations }}</p>
        </div>
    </div>

    {{-- Recent Events --}}
    <div class="bg-white rounded-xl shadow">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Event Terbaru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Judul</th>
                        <th class="px-6 py-3 text-left">Tanggal Event</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentEvents as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $event->title }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $event->event_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $event->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.events.participants', $event) }}"
                                   class="text-blue-600 hover:underline text-sm">Lihat Peserta</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada event.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
