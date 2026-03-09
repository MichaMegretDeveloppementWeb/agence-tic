<div x-data="{ showFilters: false }">
    @php
        $activeFilterCount = collect([$filterLevel, $filterStatus])->filter()->count();
    @endphp

    @error('agent-toggle-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror

    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Gestion des agents</h1>
            <p class="mt-0.5 text-[13px] text-gray-500">Créez, modifiez et gérez les comptes agents de l'agence.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button variant="secondary" size="compact" wire:click="exportCsv">
                <x-ui.icon name="arrow-down-tray" class="h-4 w-4" />
                Exporter CSV
            </x-ui.button>
            <x-ui.button href="{{ route('admin.agents.create') }}">
                <x-ui.icon name="plus" class="h-4 w-4" />
                Nouvel agent
            </x-ui.button>
        </div>
    </div>

    {{-- Recherche + toggle filtres + par page --}}
    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            {{-- Recherche --}}
            <div class="relative w-full sm:w-72">
                <x-ui.icon name="magnifying-glass" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un agent..." class="w-full rounded-lg border-0 bg-white py-1.5 pl-8 pr-3 text-[13px] text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-900" />
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
        </div>
        <x-per-page />
    </div>

    {{-- Panneau filtres dépliable --}}
    <div x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-3">
        <div class="rounded-xl border border-gray-200 bg-white px-5 py-4">
            <div class="flex flex-wrap items-end gap-x-4 gap-y-3">
                <div>
                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Niveau</label>
                    <select wire:model.live="filterLevel" class="rounded-lg border-0 py-2 pl-3 pr-8 text-[13px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
                        <option value="">Tous les niveaux</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}">Niveau {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Statut</label>
                    <select wire:model.live="filterStatus" class="rounded-lg border-0 py-2 pl-3 pr-8 text-[13px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
                        <option value="">Tous les statuts</option>
                        <option value="active">Actif</option>
                        <option value="inactive">Inactif</option>
                    </select>
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

    {{-- Tableau --}}
    <div class="mt-4">
        @if($agents->isEmpty())
            <x-ui.empty-state icon="users" title="Aucun agent trouvé" description="Aucun agent ne correspond à vos critères.">
                <x-ui.button href="{{ route('admin.agents.create') }}">
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    Nouvel agent
                </x-ui.button>
            </x-ui.empty-state>
        @else
            <x-ui.table>
                <x-ui.table.head>
                    <x-ui.table.header-cell :first="true" sortable :sorted="$sortBy === 'name' ? $sortDirection : null" wire:click="sortBy('name')">Agent</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="sm" sortable :sorted="$sortBy === 'email' ? $sortDirection : null" wire:click="sortBy('email')">E-mail</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="md" sortable :sorted="$sortBy === 'accreditation_level' ? $sortDirection : null" wire:click="sortBy('accreditation_level')">Niveau</x-ui.table.header-cell>
                    <x-ui.table.header-cell sortable :sorted="$sortBy === 'role' ? $sortDirection : null" wire:click="sortBy('role')">Statut</x-ui.table.header-cell>
                    <x-ui.table.header-cell :last="true"></x-ui.table.header-cell>
                </x-ui.table.head>
                <x-ui.table.body>
                    @foreach($agents as $agent)
                        <x-ui.table.row class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('admin.agents.show', $agent) }}'">
                            <x-ui.table.cell :first="true">
                                <div class="flex items-center gap-x-3">
                                    @if($agent->avatar_path)
                                        <x-ui.avatar :src="Storage::disk('public')->url($agent->avatar_path)" size="default" />
                                    @else
                                        <x-ui.avatar :initials="strtoupper(substr($agent->name, 0, 2))" size="default" color="gray" />
                                    @endif
                                    <div>
                                        <p class="text-[13px] font-medium text-gray-900">{{ $agent->name }}</p>
                                        <p class="text-[12px] text-gray-400">{{ $agent->agent_code }}</p>
                                    </div>
                                </div>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="sm">
                                <span class="text-[13px] text-gray-600">{{ $agent->email }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="md">
                                <x-ui.badge color="indigo">{{ $agent->accreditation_level }}</x-ui.badge>
                            </x-ui.table.cell>
                            <x-ui.table.cell>
                                @if($agent->is_active)
                                    <x-ui.badge color="emerald" dot>Actif</x-ui.badge>
                                @else
                                    <x-ui.badge color="red" dot>Inactif</x-ui.badge>
                                @endif
                            </x-ui.table.cell>
                            <x-ui.table.cell :last="true">
                                <div x-data="{ open: false, menuStyle: {} }" class="relative" @click.stop>
                                    <button @click="
                                        const rect = $el.getBoundingClientRect();
                                        const spaceBelow = window.innerHeight - rect.bottom;
                                        const above = spaceBelow < 200;
                                        menuStyle = { position: 'fixed', right: (window.innerWidth - rect.right) + 'px', ...(above ? { bottom: (window.innerHeight - rect.top + 4) + 'px' } : { top: (rect.bottom + 4) + 'px' }) };
                                        open = !open;
                                    " class="rounded-md p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                                        <x-ui.icon name="ellipsis-horizontal" class="h-4 w-4" />
                                    </button>
                                    <div x-show="open" x-transition @click.outside="open = false" :style="menuStyle"
                                         class="w-44 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200 z-[9999]">
                                        <a href="{{ route('admin.agents.show', $agent) }}" class="flex items-center gap-x-2 px-3 py-2 text-[13px] text-gray-700 hover:bg-gray-50">
                                            <x-ui.icon name="user" class="h-4 w-4 text-gray-400" />
                                            Consulter
                                        </a>
                                        <a href="{{ route('admin.agents.edit', $agent) }}" class="flex items-center gap-x-2 px-3 py-2 text-[13px] text-gray-700 hover:bg-gray-50">
                                            <x-ui.icon name="pencil-square" class="h-4 w-4 text-gray-400" />
                                            Modifier
                                        </a>
                                        <div class="my-1 border-t border-gray-100"></div>
                                        <button wire:click="toggleActive({{ $agent->id }})" wire:confirm="{{ $agent->is_active ? 'Désactiver cet agent ?' : 'Réactiver cet agent ?' }}" class="flex w-full items-center gap-x-2 px-3 py-2 text-[13px] {{ $agent->is_active ? 'text-red-600 hover:bg-red-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                            @if($agent->is_active)
                                                <x-ui.icon name="x-circle" class="h-4 w-4" />
                                                Désactiver
                                            @else
                                                <x-ui.icon name="check-circle" class="h-4 w-4" />
                                                Réactiver
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </x-ui.table.cell>
                        </x-ui.table.row>
                    @endforeach
                </x-ui.table.body>
            </x-ui.table>
            <div class="mt-4 flex items-center justify-between">
                <x-ui.pagination :paginator="$agents" mode="livewire" />
            </div>
        @endif
    </div>
</div>
