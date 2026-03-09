<div x-data="{ showFilters: false }">
    @php
        $activeFilterCount = collect([$filterStatus, $dateFrom, $dateTo])->filter()->count();
    @endphp

    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Candidatures</h1>
            <p class="mt-0.5 text-[13px] text-gray-500">Consultez et traitez les candidatures reçues via le formulaire de recrutement.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button variant="secondary" size="compact" wire:click="exportCsv">
                <x-ui.icon name="arrow-down-tray" class="h-4 w-4" />
                Exporter CSV
            </x-ui.button>
        </div>
    </div>

    {{-- Recherche + toggle filtres + par page --}}
    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            {{-- Recherche --}}
            <div class="relative w-full sm:w-72">
                <x-ui.icon name="magnifying-glass" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher une candidature..." class="w-full rounded-lg border-0 bg-white py-1.5 pl-8 pr-3 text-[13px] text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-900" />
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
                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Statut</label>
                    <select wire:model.live="filterStatus" class="rounded-lg border-0 py-2 pl-3 pr-8 text-[13px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
                        <option value="">Tous les statuts</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Du</label>
                    <input type="date" wire:model.live="dateFrom" class="rounded-lg border-0 py-1.5 text-[12px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900" />
                </div>
                <div>
                    <label class="block text-[11px] font-medium text-gray-400 mb-1">Au</label>
                    <input type="date" wire:model.live="dateTo" class="rounded-lg border-0 py-1.5 text-[12px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900" />
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="mt-4">
        @if($applications->isEmpty())
            <x-ui.empty-state icon="envelope" title="Aucune candidature" description="Aucune candidature ne correspond à vos critères." />
        @else
            <x-ui.table>
                <x-ui.table.head>
                    <x-ui.table.header-cell :first="true" sortable :sorted="$sortBy === 'name' ? $sortDirection : null" wire:click="sortBy('name')">Candidat</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="sm" sortable :sorted="$sortBy === 'email' ? $sortDirection : null" wire:click="sortBy('email')">E-mail</x-ui.table.header-cell>
                    <x-ui.table.header-cell sortable :sorted="$sortBy === 'status' ? $sortDirection : null" wire:click="sortBy('status')">Statut</x-ui.table.header-cell>
                    <x-ui.table.header-cell :last="true" hidden="md" sortable :sorted="$sortBy === 'created_at' ? $sortDirection : null" wire:click="sortBy('created_at')">Date</x-ui.table.header-cell>
                </x-ui.table.head>
                <x-ui.table.body>
                    @foreach($applications as $application)
                        <x-ui.table.row class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('admin.applications.show', $application) }}'">
                            <x-ui.table.cell :first="true">
                                <span class="text-[13px] font-medium text-gray-900">{{ $application->name }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="sm">
                                <span class="text-[13px] text-gray-600">{{ $application->email }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell>
                                <x-ui.badge :color="$application->status->badgeColor()" dot>{{ $application->status->label() }}</x-ui.badge>
                            </x-ui.table.cell>
                            <x-ui.table.cell :last="true" hidden="md">
                                <span class="text-[13px] text-gray-400">{{ $application->created_at->format('d/m/Y') }}</span>
                            </x-ui.table.cell>
                        </x-ui.table.row>
                    @endforeach
                </x-ui.table.body>
            </x-ui.table>
            <div class="mt-4 flex items-center justify-between">
                <x-ui.pagination :paginator="$applications" mode="livewire" />
            </div>
        @endif
    </div>
</div>
