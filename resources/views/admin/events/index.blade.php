<x-layouts.admin>
    <h1 class="text-xl font-bold mb-4">Kelola Event</h1>
    <p class="text-gray-500 text-sm">Halaman ini akan diimplementasi oleh Suradi.</p>

    <table class="w-full mt-4 text-sm bg-white shadow rounded">
        <thead class="bg-gray-100"><tr>
            <th class="px-4 py-2 text-left">Judul</th>
            <th class="px-4 py-2 text-left">Tanggal</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Peserta</th>
        </tr></thead>
        <tbody>
            @foreach($events as $event)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $event->title }}</td>
                <td class="px-4 py-2">{{ $event->event_date->format('d M Y') }}</td>
                <td class="px-4 py-2">{{ $event->status }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('admin.events.participants', $event) }}" class="text-blue-600 text-xs">Lihat Peserta</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.admin>
