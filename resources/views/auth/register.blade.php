<x-layouts.app>
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow mt-8">
        <h1 class="text-xl font-bold mb-4">Daftar Akun</h1>

        <form method="POST" action="/register">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full border rounded px-3 py-2 text-sm" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full border rounded px-3 py-2 text-sm" required>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">No. HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2 text-sm" required>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Daftar</button>
        </form>

        <p class="mt-4 text-sm text-center text-gray-500">
            Sudah punya akun? <a href="/login" class="text-blue-600">Login</a>
        </p>
    </div>
</x-layouts.app>
