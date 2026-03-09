<section class="bg-gray-50 px-4 py-20 sm:px-6">
    <div class="mx-auto max-w-5xl">

        <div class="text-center">
            <h2 class="text-lg font-semibold text-gray-900">Notre mission</h2>
            <p class="mt-1 text-[13px] text-gray-500">Trois piliers fondamentaux guident chacune de nos opérations.</p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-3">
            {{-- Traquer --}}
            <x-ui.card>
                <div class="flex items-center gap-x-3 mb-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-900">
                        <x-ui.icon name="magnifying-glass" class="h-4 w-4 text-white" />
                    </div>
                    <h3 class="text-[13px] font-semibold text-gray-900">Traquer</h3>
                </div>
                <p class="text-[13px] text-gray-600">
                    Identifier et localiser les anomalies avant qu'elles ne représentent une menace.
                    Nos agents sont déployés sur le terrain pour détecter toute activité anormale.
                </p>
            </x-ui.card>

            {{-- Intervenir --}}
            <x-ui.card>
                <div class="flex items-center gap-x-3 mb-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-900">
                        <x-ui.icon name="bolt" class="h-4 w-4 text-white" />
                    </div>
                    <h3 class="text-[13px] font-semibold text-gray-900">Intervenir</h3>
                </div>
                <p class="text-[13px] text-gray-600">
                    Agir avec précision et rapidité pour neutraliser ou confiner les menaces identifiées.
                    Chaque intervention suit des protocoles stricts et éprouvés.
                </p>
            </x-ui.card>

            {{-- Contrôler --}}
            <x-ui.card>
                <div class="flex items-center gap-x-3 mb-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-900">
                        <x-ui.icon name="lock-closed" class="h-4 w-4 text-white" />
                    </div>
                    <h3 class="text-[13px] font-semibold text-gray-900">Contrôler</h3>
                </div>
                <p class="text-[13px] text-gray-600">
                    Maintenir un confinement durable et surveiller les anomalies répertoriées.
                    La documentation et le suivi garantissent la pérennité de nos opérations.
                </p>
            </x-ui.card>
        </div>

    </div>
</section>
