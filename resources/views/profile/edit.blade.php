<x-layouts.app>
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-xl font-bold mb-4">Edit Profil</h1>

        <form method="POST" action="/profile">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="w-full border rounded px-3 py-2 text-sm" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full border rounded px-3 py-2 text-sm" required>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">No. HP</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Simpan</button>
        </form>
    </div>
</x-layouts.app>
