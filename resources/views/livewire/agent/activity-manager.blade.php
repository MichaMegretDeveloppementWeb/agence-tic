<div>
    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Fil d'activité</h1>
            <p class="mt-0.5 text-[13px] text-gray-500">
                Journal des événements récents de l'Agence.
            </p>
        </div>
    </div>

    {{-- Recherche + per-page --}}
    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-xs">
            <x-ui.icon name="magnifying-glass" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher une activité..." class="w-full rounded-lg border-0 bg-white py-1.5 pl-8 pr-3 text-[13px] text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-900" />
        </div>
        <x-per-page />
    </div>

    {{-- Timeline --}}
    <div class="mt-4">
        @if($entries->isEmpty())
            <x-ui.empty-state
                icon="chart-bar"
                title="Aucune activité"
                description="Aucun événement n'a été enregistré pour le moment."
            />
        @else
            <x-ui.card>
                <x-ui.timeline>
                    @foreach($entries as $entry)
                        <x-ui.timeline.item
                            :title="e($entry->message)"
                            :date="$entry->created_at->diffForHumans()"
                            :color="match($entry->event_type) {
                                'created' => 'emerald',
                                'updated' => 'blue',
                                'deleted' => 'red',
                                'login' => 'indigo',
                                default => 'gray',
                            }"
                            :icon="match($entry->event_type) {
                                'created' => 'plus',
                                'updated' => 'pencil-square',
                                'deleted' => 'trash',
                                'login' => 'user',
                                default => 'information-circle',
                            }"
                        />
                    @endforeach
                </x-ui.timeline>
            </x-ui.card>

            <div class="mt-4">
                <x-ui.pagination :paginator="$entries" mode="livewire" />
            </div>
        @endif
    </div>
</div>
