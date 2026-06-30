<x-layouts.admin>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Event</h1>
            <p class="text-sm text-gray-500">Kelola data event kampus, status, peserta, dan aksi admin.</p>
        </div>

        <a href="{{ route('admin.events.create') }}"
           class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">
            + Tambah Event
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">Judul</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Peserta</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($events as $event)
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $event->title }}
                        </td>

                        <td class="px-4 py-3 text-gray-600">
                            {{ $event->event_date->format('d M Y') }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs
                                {{ $event->status === 'open'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-gray-600">
                            {{ $event->registrations_count ?? 0 }} peserta
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.events.edit', $event) }}"
                                   class="text-blue-600 hover:underline">
                                    Edit
                                </a>

                                <a href="{{ route('admin.events.participants', $event) }}"
                                   class="text-indigo-600 hover:underline">
                                    Peserta
                                </a>

                                <form action="{{ route('admin.events.destroy', $event) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus event ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-red-600 hover:underline">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Belum ada event.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $events->links() }}
    </div>
</x-layouts.admin>
