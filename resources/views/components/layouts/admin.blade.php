<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin — Event Kampus' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 bg-white shadow-md flex-shrink-0">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800">Admin Panel</h2>
            </div>
            <nav class="p-4 flex flex-col gap-1">
                <a href="/admin" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600">Dashboard</a>
                <a href="/admin/events" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600">Kelola Event</a>
                <a href="/admin/reports" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600">Laporan</a>

                <form method="POST" action="/logout" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 rounded-lg text-red-600 hover:bg-red-50">Logout</button>
                </form>
            </nav>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 p-8">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
