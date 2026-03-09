<div>
    {{-- Formulaire de recherche --}}
    <x-ui.card>
        <form wire:submit="search" class="space-y-4">
            <div>
                <label for="trackingCode" class="block text-[13px] font-medium text-gray-700 mb-1.5">Code de suivi</label>
                <input
                    type="text"
                    id="trackingCode"
                    wire:model.blur="trackingCode"
                    placeholder="TIC-XXXXXXXX"
                    maxlength="12"
                    class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('trackingCode') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset uppercase tracking-wider"
                />
                @error('trackingCode')
                    <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end">
                <x-ui.button type="submit" :loading="true" target="search">
                    <x-ui.icon name="magnifying-glass" class="h-4 w-4" />
                    Rechercher
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Erreur : candidature non trouvee --}}
    @error('tracking-not-found')
        <div class="mt-5">
            <x-ui.alert type="error" dismissible>{{ $message }}</x-ui.alert>
        </div>
    @enderror

    {{-- Resultat : candidature trouvee --}}
    @if($application)
        <div class="mt-5">
            <x-ui.card>
                <h2 class="text-[13px] font-semibold text-gray-900 mb-4">Votre candidature</h2>

                <x-ui.description-list variant="inline">
                    <x-ui.description-list.item label="Nom" variant="inline">
                        {{ $application->name }}
                    </x-ui.description-list.item>

                    <x-ui.description-list.item label="E-mail" variant="inline">
                        {{ $application->email }}
                    </x-ui.description-list.item>

                    <x-ui.description-list.item label="Statut" variant="inline">
                        <x-ui.badge :color="$application->status->badgeColor()" dot>
                            {{ $application->status->label() }}
                        </x-ui.badge>
                    </x-ui.description-list.item>

                    <x-ui.description-list.item label="Date de soumission" variant="inline">
                        {{ $application->created_at->translatedFormat('d F Y à H:i') }}
                    </x-ui.description-list.item>
                </x-ui.description-list>
            </x-ui.card>
        </div>
    @endif
</div>
