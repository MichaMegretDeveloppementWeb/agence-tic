<header class="border-b border-gray-200 bg-white">
    <nav class="mx-auto flex max-w-[90em] items-center justify-between px-4 py-4 sm:px-6">
        <a href="{{ route('home') }}" class="text-[15px] font-semibold tracking-tight text-gray-900">
            Agence TIC
        </a>
        <div class="flex items-center gap-x-4">
            <a href="{{ route('recruitment') }}" class="text-[13px] font-medium text-gray-600 hover:text-gray-900">
                Recrutement
            </a>
            <x-ui.button variant="primary" size="compact" href="{{ route('login') }}">
                Connexion
            </x-ui.button>
        </div>
    </nav>
</header>
