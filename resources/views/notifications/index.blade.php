<x-layouts.app title="Notifikasi">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Notifikasi</h1>

        @if($notifications->isEmpty())
            <div class="bg-white rounded-lg p-8 text-center text-gray-500 border border-gray-200">
                Tidak ada notifikasi.
            </div>
        @else
            <div class="space-y-3">
                @foreach($notifications as $notification)
                    <div class="rounded-lg border border-gray-200 p-4 {{ $notification->isRead() ? 'bg-white' : 'bg-blue-50' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    @if($notification->type === 'confirm')
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded">Konfirmasi</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 rounded">Pengingat</span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-800 text-sm">{{ $notification->message }}</p>
                            </div>

                            @if(!$notification->isRead())
                                <form action="{{ route('notifications.read', $notification) }}" method="POST" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 border border-blue-300 rounded px-2 py-1 hover:bg-blue-100">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
