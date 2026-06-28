<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Event</h1>
        <p class="text-gray-500 mt-1">Temukan dan daftar event kampus yang tersedia</p>
    </div>

    @if($events->isEmpty())
        <div class="text-center py-12 text-gray-500">
            <p>Belum ada event yang tersedia saat ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    @if($event->poster_img)
                        <img src="{{ Storage::url($event->poster_img) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">Tidak ada poster</span>
                        </div>
                    @endif

                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h2 class="text-lg font-semibold text-gray-800 leading-tight">{{ $event->title }}</h2>
                            @if($event->status === 'open')
                                <span class="ml-2 shrink-0 inline-block px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Buka</span>
                            @else
                                <span class="ml-2 shrink-0 inline-block px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Tutup</span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 mb-4">
                            {{ $event->event_date->format('d M Y') }}
                        </p>

                        <a href="{{ route('events.show', $event) }}" class="block w-full text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $events->links() }}
        </div>
    @endif
</x-layouts.app>
