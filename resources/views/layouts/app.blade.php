<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-50">
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
</head>
<body class="h-full antialiased">

    {{-- Sidebar --}}
    @php $currentRoute = Route::currentRouteName(); @endphp

    <x-ui.sidebar brand="Agence TIC" brandIcon="bolt" brandBg="bg-gray-900">
        <x-ui.sidebar.group>
            <x-ui.sidebar.link :href="route('dashboard')" icon="home"
                :active="$currentRoute === 'dashboard'">Dashboard</x-ui.sidebar.link>
            <x-ui.sidebar.link :href="route('reports.index')" icon="clipboard-document-list"
                :active="str_starts_with($currentRoute ?? '', 'reports')">Rapports</x-ui.sidebar.link>
            <x-ui.sidebar.link :href="route('library.index')" icon="folder"
                :active="str_starts_with($currentRoute ?? '', 'library')">Bibliothèque</x-ui.sidebar.link>
            <x-ui.sidebar.link :href="route('categories.index')" icon="squares-2x2"
                :active="str_starts_with($currentRoute ?? '', 'categories')">Catégories</x-ui.sidebar.link>
            <x-ui.sidebar.link :href="route('reminders.index')" icon="bell"
                :active="str_starts_with($currentRoute ?? '', 'reminders')">Rappels</x-ui.sidebar.link>
            <x-ui.sidebar.link :href="route('activity.index')" icon="chart-bar"
                :active="str_starts_with($currentRoute ?? '', 'activity')">Activité</x-ui.sidebar.link>
        </x-ui.sidebar.group>

        @if(auth()->user()?->isDirectorG())
            <x-ui.sidebar.group label="Administration">
                <x-ui.sidebar.link :href="route('admin.agents.index')" icon="users"
                    :active="str_starts_with($currentRoute ?? '', 'admin.agents')">Agents</x-ui.sidebar.link>
                <x-ui.sidebar.link :href="route('admin.permissions.index')" icon="lock-closed"
                    :active="str_starts_with($currentRoute ?? '', 'admin.permissions')">Permissions</x-ui.sidebar.link>
                <x-ui.sidebar.link :href="route('admin.applications.index')" icon="envelope"
                    :active="str_starts_with($currentRoute ?? '', 'admin.applications')">Candidatures</x-ui.sidebar.link>
            </x-ui.sidebar.group>
        @endif

        <x-slot:user>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button" class="flex w-full items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                    @if(auth()->user()?->avatar_path)
                        <x-ui.avatar :src="Storage::disk('public')->url(auth()->user()->avatar_path)" size="sm" />
                    @else
                        <x-ui.avatar :initials="strtoupper(substr(auth()->user()?->name ?? 'A', 0, 2))" size="sm" color="gray" />
                    @endif
                    <div class="min-w-0 flex-1 text-left lg:opacity-0 lg:max-w-0 lg:overflow-hidden lg:group-hover/sidebar:opacity-100 lg:group-hover/sidebar:max-w-[200px] wide:opacity-100 wide:max-w-[200px] transition-[opacity,max-width] duration-300 ease-in-out">
                        <p class="truncate text-[13px] font-medium text-gray-900">{{ auth()->user()?->name ?? 'Agent' }}</p>
                        <p class="truncate text-[12px] text-gray-400">{{ auth()->user()?->email ?? '' }}</p>
                    </div>
                    <x-ui.icon name="ellipsis-horizontal" class="h-4 w-4 shrink-0 text-gray-400 lg:opacity-0 lg:max-w-0 lg:overflow-hidden lg:group-hover/sidebar:opacity-100 lg:group-hover/sidebar:max-w-[32px] wide:opacity-100 wide:max-w-[32px] transition-[opacity,max-width] duration-300 ease-in-out" />
                </button>
                <div x-show="open" x-transition @click.outside="open = false" class="absolute bottom-full left-2 right-2 mb-1 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200 z-50">
                    <a href="{{ route('profile') }}" class="flex w-full items-center gap-x-2 px-3 py-2 text-[13px] text-gray-700 hover:bg-gray-50">
                        <x-ui.icon name="user" class="h-4 w-4" />
                        Mon profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-x-2 px-3 py-2 text-[13px] text-red-600 hover:bg-red-50">
                            <x-ui.icon name="arrow-left" class="h-4 w-4" />
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </x-slot:user>
    </x-ui.sidebar>

    {{-- Content wrapper --}}
    <div class="flex h-full flex-col lg:pl-[62px] wide:pl-[260px]">
        {{-- Topbar --}}
        <header class="flex h-14 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6">
            <div class="flex items-center gap-x-3">
                <x-ui.sidebar.trigger />
                <button @click="$dispatch('open-global-search')" class="hidden items-center gap-x-2 rounded-lg bg-gray-50 px-3 py-1.5 text-[13px] text-gray-400 hover:bg-gray-100 sm:flex">
                    <x-ui.icon name="magnifying-glass" class="h-4 w-4" />
                    <span>Rechercher...</span>
                    <kbd class="rounded border border-gray-200 px-1.5 py-0.5 text-[10px] font-medium">&#8984;K</kbd>
                </button>
            </div>
            <div class="flex items-center gap-x-3">
                @livewire('agent.notification-bell')
                <span class="text-[12px] font-medium text-gray-400">
                    Niveau {{ auth()->user()?->accreditation_level ?? '?' }}
                </span>
                @if(auth()->user()?->avatar_path)
                    <x-ui.avatar :src="Storage::disk('public')->url(auth()->user()->avatar_path)" size="sm" />
                @else
                    <x-ui.avatar :initials="strtoupper(substr(auth()->user()?->name ?? 'A', 0, 2))" size="sm" color="gray" />
                @endif
            </div>
        </header>

        {{-- Main content --}}
        <main class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-[90em] px-4 py-6 sm:px-6 sm:py-8">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Global search --}}
    @auth
        @livewire('agent.global-search')
    @endauth

    {{-- Redirect loading overlay --}}
    <div id="redirect-overlay" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/20 backdrop-blur-[2px]">
        <div class="flex items-center gap-x-3 rounded-xl bg-white px-5 py-3 shadow-lg ring-1 ring-gray-200">
            <svg class="h-5 w-5 animate-spin text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-[13px] font-medium text-gray-900">Chargement...</span>
        </div>
    </div>

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
