<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — {{ config('app.name', 'Agence TIC') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased">
    <div class="flex min-h-full items-center justify-center px-4 py-12 sm:px-6">
        <div class="w-full max-w-md text-center">
            {{-- Icon --}}
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full @yield('icon-bg', 'bg-gray-100')">
                @yield('icon')
            </div>

            {{-- Error code --}}
            <p class="mt-6 text-2xl font-semibold tracking-tight text-gray-900">@yield('code')</p>

            {{-- Title --}}
            <h1 class="mt-2 text-lg font-semibold text-gray-900">@yield('heading')</h1>

            {{-- Message --}}
            <p class="mt-2 text-[13px] text-gray-600">@yield('message')</p>

            {{-- Action --}}
            <div class="mt-8">
                @yield('action')
            </div>

            {{-- Footer --}}
            <p class="mt-10 text-[12px] text-gray-400">{{ config('app.name', 'Agence TIC') }}</p>
        </div>
    </div>
</body>
</html>
