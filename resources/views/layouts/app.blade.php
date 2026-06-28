<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Event Kampus' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="text-xl font-bold text-blue-600">Event Kampus</a>

                <div class="flex items-center gap-4">
                    <a href="/events" class="text-gray-700 hover:text-blue-600">Events</a>

                    @auth
                        <a href="/notifications" class="text-gray-700 hover:text-blue-600 relative">
                            Notifikasi
                            @if(auth()->user()->notifications()->whereNull('read_at')->count() > 0)
                                <span class="inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full ml-1">
                                    {{ auth()->user()->notifications()->whereNull('read_at')->count() }}
                                </span>
                            @endif
                        </a>
                        <a href="/profile" class="text-gray-700 hover:text-blue-600">Profile</a>
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-blue-600">Logout</button>
                        </form>
                    @else
                        <a href="/login" class="text-gray-700 hover:text-blue-600">Login</a>
                        <a href="/register" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{ $slot }}
    </main>
</body>
</html>
