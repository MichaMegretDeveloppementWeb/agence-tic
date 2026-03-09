<section class="bg-gray-900 px-4 py-24 sm:px-6 sm:py-32">
    <div class="mx-auto max-w-3xl text-center">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Classification : Publique</p>

        <h1 class="mt-4 text-4xl font-bold tracking-tight text-white sm:text-5xl">
            Agence TIC
        </h1>

        <p class="mt-2 text-lg font-medium text-gray-300">
            Traquer · Intervenir · Contrôler
        </p>

        <p class="mx-auto mt-6 max-w-xl text-[15px] leading-relaxed text-gray-400">
            L'Agence TIC est une organisation dédiée à l'identification, au confinement et à la neutralisation
            d'anomalies. Notre mission est de protéger la population en opérant dans l'ombre,
            avec rigueur, discrétion et détermination.
        </p>

        <div class="mt-10 flex items-center justify-center gap-x-4">
            <x-ui.button href="{{ route('recruitment') }}">
                Rejoindre l'Agence
            </x-ui.button>
            <x-ui.button variant="ghost" href="{{ route('login') }}" class="text-gray-300 hover:text-white hover:bg-gray-800">
                Accès agents
                <x-ui.icon name="arrow-up-right" class="h-4 w-4" />
            </x-ui.button>
        </div>
    </div>
</section>
