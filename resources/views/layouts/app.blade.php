<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Oversee')</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background text-ink min-h-screen">
    @auth
        <div class="flex gap-4 p-4 min-h-screen">
            <x-sidebar />

            <div class="flex-1 min-w-0">
                @if (session('success'))
                    <x-alert class="mb-4">{{ session('success') }}</x-alert>
                @endif

                <main>@yield('content')</main>
            </div>
        </div>
    @else
        @if (session('success'))
            <x-alert class="mb-4">{{ session('success') }}</x-alert>
        @endif

        <main>@yield('content')</main>
    @endauth
</body>

</html>
