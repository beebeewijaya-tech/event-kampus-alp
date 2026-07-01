<x-layouts.app>
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center py-12">
        <div>
            <span class="inline-block mb-4 px-4 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                Pendaftaran Event Kampus
            </span>

            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">
                Temukan event kampus dan daftar dalam satu klik
            </h1>

            <p class="mt-5 text-gray-600 text-lg leading-relaxed">
                Pilih seminar, workshop, lomba, dan kegiatan kampus. Kuota dan batas tanggal registrasi ditampilkan secara jelas agar peserta tidak salah daftar.
            </p>

            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('events.index') }}"
                   class="px-6 py-3 rounded-lg bg-slate-700 text-white font-semibold hover:bg-slate-800">
                    Lihat Event
                </a>

                <a href="{{ route('registrations.index') }}"
                   class="px-6 py-3 rounded-lg border border-slate-700 text-slate-700 font-semibold hover:bg-slate-700 hover:text-white">
                    Riwayat Saya
                </a>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($latestEvents as $event)
                @php
                    $totalQuota = $event->categories->sum('quota');

                    $confirmedCount = $event->categories->sum(function ($cat) {
                        return $cat->registrations->where('status', 'confirmed')->count();
                    });

                    $isFull = $totalQuota > 0 && $confirmedCount >= $totalQuota;
                @endphp

                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <h2 class="font-bold text-gray-900">
                                {{ $event->title }}
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ $event->event_date->format('d M Y') }}
                            </p>
                        </div>

                        @if($isFull)
                            <span class="px-3 py-1 rounded-lg bg-red-600 text-white text-xs font-semibold">
                                Penuh
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-lg bg-green-600 text-white text-xs font-semibold">
                                {{ $confirmedCount }}/{{ $totalQuota }}
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('events.show', $event) }}"
                       class="inline-block mt-4 text-sm font-semibold text-blue-600 hover:underline">
                        Lihat detail →
                    </a>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-xl p-6 text-gray-500">
                    Belum ada event yang tersedia.
                </div>
            @endforelse
        </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-10">
        <div class="bg-white border rounded-xl p-6">
            <h3 class="font-bold text-gray-900 mb-2">Event Terbaru</h3>
            <p class="text-sm text-gray-500">
                Peserta dapat melihat daftar event yang masih dibuka.
            </p>
        </div>

        <div class="bg-white border rounded-xl p-6">
            <h3 class="font-bold text-gray-900 mb-2">Pendaftaran Mudah</h3>
            <p class="text-sm text-gray-500">
                Peserta bisa memilih kategori tiket dan mendaftar dengan cepat.
            </p>
        </div>

        <div class="bg-white border rounded-xl p-6">
            <h3 class="font-bold text-gray-900 mb-2">Kuota Transparan</h3>
            <p class="text-sm text-gray-500">
                Kuota dan status penuh ditampilkan agar peserta tidak salah daftar.
            </p>
        </div>
    </section>
</x-layouts.app>
