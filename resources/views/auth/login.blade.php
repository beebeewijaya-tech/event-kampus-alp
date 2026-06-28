<x-layouts.app>
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow mt-8">
        <h1 class="text-xl font-bold mb-4">Login</h1>

        @if(session('error'))
            <div class="mb-3 p-3 bg-red-100 text-red-600 rounded text-sm">{{ session('error') }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full border rounded px-3 py-2 text-sm" required>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Login</button>
        </form>

        <p class="mt-4 text-sm text-center text-gray-500">
            Belum punya akun? <a href="/register" class="text-blue-600">Daftar</a>
        </p>
    </div>
</x-layouts.app>
