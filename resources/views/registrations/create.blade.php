<x-layouts.app>
    <div class="mb-4">
        <a href="{{ route('events.show', $event) }}" class="text-blue-600 text-sm">&larr; Kembali</a>
    </div>

    <div class="max-w-xl mx-auto bg-white rounded p-6 shadow">
        <h1 class="text-xl font-bold mb-4">Daftar: {{ $event->title }}</h1>

        <div class="mb-4 text-sm text-gray-600">
            <p><strong>Nama:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>No. HP:</strong> {{ $user->phone ?? '-' }}</p>
        </div>

        <form action="{{ route('registrations.store', $event) }}" method="POST">
            @csrf

            <p class="font-medium mb-2">Pilih Kategori:</p>

            @error('event_category_id')
                <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
            @enderror

            @foreach($event->categories as $category)
                @php
                    $terisi = $category->registrations()->where('status', 'confirmed')->count();
                    $penuh = $terisi >= $category->quota;
                @endphp
                <label class="block border rounded p-3 mb-2 cursor-pointer">
                    <input type="radio" name="event_category_id" value="{{ $category->id }}"
                        {{ $selectedCategoryId == $category->id ? 'checked' : '' }}>
                    <strong>{{ $category->name }}</strong>
                    — Rp {{ number_format($category->price, 0, ',', '.') }}
                    @if($penuh)
                        <span class="text-yellow-600 text-sm">(Waiting List)</span>
                    @else
                        <span class="text-gray-500 text-sm">(Sisa: {{ $category->quota - $terisi }} kursi)</span>
                    @endif
                </label>
            @endforeach

            <button type="submit" class="mt-4 w-full bg-blue-600 text-white py-2 rounded">
                Daftar Sekarang
            </button>
        </form>
    </div>
</x-layouts.app>
