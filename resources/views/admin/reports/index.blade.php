<x-layouts.admin>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan / Statistik</h1>
        <p class="text-sm text-gray-500">
            Ringkasan data event, pendaftaran, waiting list, dan check-in peserta.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Total Event</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalEvents }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Total Kategori</p>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $totalCategories }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Total Pendaftaran</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalRegistrations }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Terkonfirmasi</p>
            <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $confirmedRegistrations }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Waiting List</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $waitingListRegistrations }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Check-in Hari Ini</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $checkedInToday }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-5 border-b">
                <h2 class="font-bold text-gray-800">Event dengan Pendaftar Terbanyak</h2>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">Event</th>
                        <th class="px-4 py-3 text-left">Jumlah Pendaftar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($popularEvents as $event)
                        <tr class="border-t">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $event->title }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $event->registrations_count }} peserta
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-6 text-center text-gray-500">
                                Belum ada data event.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-5 border-b">
                <h2 class="font-bold text-gray-800">Pendaftaran Terbaru</h2>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">Peserta</th>
                        <th class="px-4 py-3 text-left">Event</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestRegistrations as $registration)
                        <tr class="border-t">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $registration->user?->name ?? 'User tidak tersedia' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $registration->eventCategory?->event?->title ?? 'Event tidak tersedia' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($registration->status === 'confirmed')
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                        Confirmed
                                    </span>
                                @elseif($registration->status === 'waiting_list')
                                    <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                                        Waiting List
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                        {{ ucfirst($registration->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                                Belum ada pendaftaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
