<div x-data="{ showFilters: false }">
    @php
        $activeFilterCount = collect([$filterType, $filterPriority, $showCompleted])->filter()->count();
    @endphp

    @error('reminder-toggle-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror
    @error('reminder-deletion-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror

    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Rappels</h1>
            <p class="mt-0.5 text-[13px] text-gray-500">
                Gérez vos rappels personnels et consultez les rappels ciblés et globaux.
            </p>
        </div>
        <x-ui.button :href="route('reminders.create')">
            <x-ui.icon name="plus" class="h-4 w-4" />
            Nouveau rappel
        </x-ui.button>
    </div>

    {{-- Recherche + toggle filtres + tri + par page --}}
    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            {{-- Recherche --}}
            <div class="relative w-full sm:w-72">
                <x-ui.icon name="magnifying-glass" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un rappel..." class="w-full rounded-lg border-0 bg-white py-1.5 pl-8 pr-3 text-[13px] text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-900" />
            </div>
            {{-- Toggle filtres --}}
            <button @click="showFilters = !showFilters" type="button" class="inline-flex items-center gap-x-1.5 rounded-lg bg-white px-3 py-1.5 text-[13px] font-medium text-gray-700 ring-1 ring-inset ring-gray-200 hover:bg-gray-50">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                </svg>
                Filtres
                @if($activeFilterCount > 0)
                    <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-gray-900 px-1.5 text-[11px] font-semibold text-white">{{ $activeFilterCount }}</span>
                @endif
            </button>
            {{-- Tri (reste visible) --}}
            <select wire:model.live="sortBy" class="rounded-lg border-0 py-1.5 pl-3 pr-8 text-[13px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
                <option value="created_at">Trier par date</option>
                <option value="title">Trier par titre</option>
                <option value="type">Trier par type</option>
                <option value="due_date">Trier par échéance</option>
            </select>
            <button wire:click="sortBy('{{ $sortBy }}')" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 ring-1 ring-inset ring-gray-200" title="Inverser l'ordre">
                @if($sortDirection === 'asc')
                    <x-ui.icon name="arrow-up-right" class="h-4 w-4" />
                @else
                    <x-ui.icon name="arrow-down-right" class="h-4 w-4" />
                @endif
            </button>
        </div>
        <x-per-page />
    </div>

    {{-- Panneau filtres dépliable --}}
    <div x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-3">
        <div class="rounded-xl border border-gray-200 bg-white px-5 py-4">
            <div class="flex flex-wrap items-end gap-x-4 gap-y-3">
                <div>
                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Type</label>
                    <select wire:model.live="filterType" class="rounded-lg border-0 py-2 pl-3 pr-8 text-[13px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
                        <option value="">Tous les types</option>
                        @foreach($reminderTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Priorité</label>
                    <select wire:model.live="filterPriority" class="rounded-lg border-0 py-2 pl-3 pr-8 text-[13px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
                        <option value="">Toutes les priorités</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end pb-0.5">
                    <label class="flex items-center gap-x-2 text-[13px] text-gray-600 cursor-pointer">
                        <input type="checkbox" wire:model.live="showCompleted" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900" />
                        Afficher complétés
                    </label>
                </div>
                @if($activeFilterCount > 0)
                    <button wire:click="resetFilters" type="button" class="inline-flex items-center gap-x-1 rounded-lg px-2.5 py-1.5 text-[12px] font-medium text-red-600 hover:bg-red-50">
                        <x-ui.icon name="x-mark" class="h-3.5 w-3.5" />
                        Réinitialiser
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Liste des rappels --}}
    <div class="mt-4">
        @if($reminders->isEmpty())
            <x-ui.empty-state
                icon="bell"
                title="Aucun rappel"
                :description="$search || $filterType || $filterPriority
                    ? 'Aucun rappel ne correspond à vos critères.'
                    : 'Vous n\'avez aucun rappel actif.'"
            >
                <x-ui.button :href="route('reminders.create')">
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    Nouveau rappel
                </x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="space-y-3">
                @foreach($reminders as $reminder)
                    <x-ui.card>
                        <div class="flex items-start gap-x-4">
                            {{-- Checkbox (personnel uniquement) --}}
                            <div class="mt-0.5 shrink-0">
                                @if($reminder->type === \App\Enums\ReminderType::Personal && $reminder->created_by === auth()->id())
                                    <button
                                        wire:click="toggleComplete({{ $reminder->id }})"
                                        class="flex h-5 w-5 items-center justify-center rounded border {{ $reminder->is_completed ? 'border-gray-900 bg-gray-900' : 'border-gray-300 hover:border-gray-400' }}"
                                    >
                                        @if($reminder->is_completed)
                                            <x-ui.icon name="check" class="h-3 w-3 text-white" />
                                        @endif
                                    </button>
                                @else
                                    <div class="flex h-5 w-5 items-center justify-center">
                                        <x-ui.icon name="bell" class="h-4 w-4 text-gray-300" />
                                    </div>
                                @endif
                            </div>

                            {{-- Contenu --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-x-2">
                                    <p class="text-[13px] font-medium {{ $reminder->is_completed ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                        {{ $reminder->title }}
                                    </p>
                                    <x-new-badge :date="$reminder->created_at" :read="$readReminderIds->contains($reminder->id)" />
                                    <x-ui.badge :color="$reminder->type->badgeColor()" dot>
                                        {{ $reminder->type->label() }}
                                    </x-ui.badge>
                                    <x-ui.badge :color="$reminder->priority?->badgeColor() ?? 'blue'">
                                        {{ $reminder->priority?->label() ?? 'Normale' }}
                                    </x-ui.badge>
                                </div>
                                @if($reminder->content)
                                    <p class="mt-1 text-[12px] text-gray-400">{{ $reminder->content }}</p>
                                @endif
                                <div class="mt-1.5 flex items-center gap-x-3">
                                    @if($reminder->due_date)
                                        <span class="text-[11px] {{ $reminder->due_date->isPast() && !$reminder->is_completed ? 'text-red-500 font-medium' : 'text-gray-400' }}">
                                            Échéance : {{ $reminder->due_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                    <span class="text-[11px] text-gray-400">
                                        {{ $reminder->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            {{-- Actions (personnel uniquement) --}}
                            @if($reminder->type === \App\Enums\ReminderType::Personal && $reminder->created_by === auth()->id())
                                <div class="shrink-0">
                                    <x-ui.tooltip text="Supprimer">
                                        <button wire:click="deleteReminder({{ $reminder->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer ce rappel ?" class="text-gray-400 hover:text-red-500">
                                            <x-ui.icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </x-ui.tooltip>
                                </div>
                            @endif
                        </div>
                    </x-ui.card>
                @endforeach
            </div>

            <div class="mt-4 flex items-center justify-between">
                <x-ui.pagination :paginator="$reminders" mode="livewire" />
            </div>
        @endif
    </div>
</div>
