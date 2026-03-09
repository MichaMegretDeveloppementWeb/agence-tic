<div>
    {{-- Filtres --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-xs">
            <x-ui.icon name="magnifying-glass" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par code ou titre..." class="w-full rounded-lg border-0 bg-gray-50 py-1.5 pl-8 pr-3 text-[13px] text-gray-900 placeholder:text-gray-400 focus:bg-white focus:ring-1 focus:ring-gray-300" />
        </div>
        <div class="flex items-center gap-x-2">
            <select wire:model.live="filterCategory" class="rounded-lg border-0 py-2 pl-3 pr-8 text-[13px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterStatus" class="rounded-lg border-0 py-2 pl-3 pr-8 text-[13px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
                <option value="">Tous les statuts</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterThreatLevel" class="hidden sm:block rounded-lg border-0 py-2 pl-3 pr-8 text-[13px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
                <option value="">Tous les niveaux</option>
                @foreach($threatLevels as $level)
                    <option value="{{ $level->value }}">{{ $level->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="mt-4">
        @if($reports->isEmpty())
            <x-ui.empty-state
                icon="clipboard-document-list"
                title="Aucun rapport trouvé"
                :description="$search || $filterCategory || $filterStatus || $filterThreatLevel
                    ? 'Aucun rapport ne correspond à vos critères de recherche.'
                    : 'Aucun rapport n\'est accessible à votre niveau d\'accréditation.'"
            />
        @else
            <x-ui.table>
                <x-ui.table.head>
                    <x-ui.table.header-cell :first="true" :sortable="true" :sorted="$sortBy === 'code' ? $sortDirection : null" wire:click="sort('code')">Code</x-ui.table.header-cell>
                    <x-ui.table.header-cell :sortable="true" :sorted="$sortBy === 'title' ? $sortDirection : null" wire:click="sort('title')">Titre</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="sm">Catégorie</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="md">Menace</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="sm">Statut</x-ui.table.header-cell>
                    <x-ui.table.header-cell :last="true" hidden="md">Niveau</x-ui.table.header-cell>
                </x-ui.table.head>
                <x-ui.table.body>
                    @foreach($reports as $report)
                        <x-ui.table.row class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('reports.show', $report) }}'">
                            <x-ui.table.cell :first="true">
                                <span class="text-[13px] font-medium text-gray-900">{{ $report->code }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell>
                                <span class="text-[13px] text-gray-600">{{ Str::limit($report->title, 40) }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="sm">
                                <span class="text-[13px] text-gray-600">{{ $report->category?->name ?? '—' }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="md">
                                <x-ui.badge :color="$report->threat_level->badgeColor()" dot>{{ $report->threat_level->label() }}</x-ui.badge>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="sm">
                                <x-ui.badge :color="$report->status->badgeColor()">{{ $report->status->label() }}</x-ui.badge>
                            </x-ui.table.cell>
                            <x-ui.table.cell :last="true" hidden="md">
                                <span class="text-[11px] font-medium text-gray-400">{{ $report->accreditation_level }}</span>
                            </x-ui.table.cell>
                        </x-ui.table.row>
                    @endforeach
                </x-ui.table.body>
            </x-ui.table>

            <div class="mt-4">
                <x-ui.pagination :paginator="$reports" mode="livewire" />
            </div>
        @endif
    </div>
</div>
