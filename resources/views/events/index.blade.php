<x-layouts.app>
    <h1 class="text-2xl font-bold mb-6">Daftar Event</h1>

    @if($events->isEmpty())
        <p class="text-gray-500">Belum ada event yang tersedia.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($events as $event)
                <div class="bg-white rounded shadow overflow-hidden">
                    @if($event->poster_img && $event->poster_img !== 'posters/default.jpg')
                        <img src="{{ Storage::url($event->poster_img) }}" class="w-full h-40 object-cover">
                    @else
                        <div class="w-full h-40 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                            Tidak ada poster
                        </div>
                    @endif

                    <div class="p-4">
                        <h2 class="font-semibold text-gray-800 mb-1">{{ $event->title }}</h2>
                        <p class="text-sm text-gray-500 mb-1">{{ $event->event_date->format('d M Y') }}</p>

                        @if($event->status === 'open')
                            <span class="text-xs text-green-600">Pendaftaran Dibuka</span>
                        @else
                            <span class="text-xs text-red-500">Ditutup</span>
                        @endif

                        <a href="{{ route('events.show', $event) }}"
                           class="block mt-3 text-center bg-blue-600 text-white py-1.5 rounded text-sm">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $events->links() }}</div>
    @endif
</x-layouts.app>
