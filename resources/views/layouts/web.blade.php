<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Agence TIC') — {{ config('app.name', 'Agence TIC') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Assets specifiques a la page --}}
    @yield('assets')
</head>
<body class="h-full antialiased">
    <x-layout.header />

    <main>
        @yield('content')
    </main>

    <x-layout.footer />

    {{-- Toast notifications --}}
    <x-ui.toast />

    @if(session('toast-success') || session('toast-error') || session('toast-warning') || session('toast-info'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(session('toast-success'))
                    window.showToast('success', 'Succès', @js(session('toast-success')));
                @endif
                @if(session('toast-error'))
                    window.showToast('error', 'Erreur', @js(session('toast-error')));
                @endif
                @if(session('toast-warning'))
                    window.showToast('warning', 'Attention', @js(session('toast-warning')));
                @endif
                @if(session('toast-info'))
                    window.showToast('info', 'Information', @js(session('toast-info')));
                @endif
            });
        </script>
    @endif

    @livewireScripts
</body>
</html>
