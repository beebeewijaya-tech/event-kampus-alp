<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Event</h1>
        <p class="text-sm text-gray-500 mt-1">
            Cari event kampus, filter kategori, dan lihat status kuota.
        </p>
    </div>

    <form method="GET" action="{{ route('events.index') }}" class="mb-5">
        <div class="flex gap-3">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="Cari nama event atau kategori..."
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">

            <button type="submit"
                    class="px-6 py-2 rounded-lg bg-slate-700 text-white text-sm font-semibold hover:bg-slate-800">
                Cari
            </button>
        </div>

        <div class="flex flex-wrap gap-3 mt-4">
            <a href="{{ route('events.index', ['search' => $search]) }}"
               class="px-6 py-2 rounded-lg text-sm font-semibold
               {{ empty($category) ? 'bg-slate-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>

            @foreach($categories as $cat)
                <a href="{{ route('events.index', ['search' => $search, 'category' => $cat]) }}"
                   class="px-6 py-2 rounded-lg text-sm font-semibold
                   {{ $category === $cat ? 'bg-slate-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </form>

    @if($events->isEmpty())
        <div class="bg-white border rounded-xl p-6 text-center text-gray-500">
            Belum ada event yang tersedia.
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            @foreach($events as $event)
                @php
                    $totalQuota = $event->categories->sum('quota');

                    $confirmedCount = $event->categories->sum(function ($cat) {
                        return $cat->registrations->where('status', 'confirmed')->count();
                    });

                    $isFull = $totalQuota > 0 && $confirmedCount >= $totalQuota;
                @endphp

                <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        @if($event->poster_img && $event->poster_img !== 'posters/default.jpg' && Storage::disk('public')->exists($event->poster_img))
                            <img src="{{ Storage::url($event->poster_img) }}"
                                 class="w-24 h-24 object-cover rounded">
                        @else
                            <div class="w-24 h-24 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400">
                                Poster
                            </div>
                        @endif

                        <div>
                            <h2 class="font-bold text-gray-900">
                                {{ $event->title }}
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ $event->event_date->format('d M Y') }}
                            </p>

                            @if($event->categories->isNotEmpty())
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $event->categories->pluck('name')->join(', ') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        @if($isFull)
                            <span class="inline-block px-3 py-1 rounded-lg bg-red-600 text-white text-sm font-semibold">
                                Kuota Penuh
                            </span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-lg bg-green-600 text-white text-sm font-semibold">
                                Kuota {{ $confirmedCount }}/{{ $totalQuota }}
                            </span>
                        @endif

                        <a href="{{ route('events.show', $event) }}"
                           class="block mt-3 px-6 py-1.5 rounded-lg border border-slate-700 text-slate-700 text-sm font-semibold hover:bg-slate-700 hover:text-white">
                            Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif
</x-layouts.app>
