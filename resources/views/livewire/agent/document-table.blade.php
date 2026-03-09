<div>
    {{-- Filtres --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-xs">
            <x-ui.icon name="magnifying-glass" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un document..." class="w-full rounded-lg border-0 bg-gray-50 py-1.5 pl-8 pr-3 text-[13px] text-gray-900 placeholder:text-gray-400 focus:bg-white focus:ring-1 focus:ring-gray-300" />
        </div>
        <div class="flex items-center gap-x-2">
            <select wire:model.live="filterCategory" class="rounded-lg border-0 py-2 pl-3 pr-8 text-[13px] text-gray-600 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-gray-900">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="mt-4">
        @if($documents->isEmpty())
            <x-ui.empty-state
                icon="folder"
                title="Aucun document trouvé"
                :description="$search || $filterCategory
                    ? 'Aucun document ne correspond à vos critères de recherche.'
                    : 'Aucun document n\'est accessible à votre niveau d\'accréditation.'"
            />
        @else
            <x-ui.table>
                <x-ui.table.head>
                    <x-ui.table.header-cell :first="true" :sortable="true" :sorted="$sortBy === 'title' ? $sortDirection : null" wire:click="sort('title')">Document</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="sm">Catégorie</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="md">Ajouté par</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="md">Taille</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="sm">Niveau</x-ui.table.header-cell>
                    <x-ui.table.header-cell :last="true"></x-ui.table.header-cell>
                </x-ui.table.head>
                <x-ui.table.body>
                    @foreach($documents as $document)
                        <x-ui.table.row class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('library.show', $document) }}'">
                            <x-ui.table.cell :first="true">
                                <div class="flex items-center gap-x-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-50">
                                        <x-ui.icon name="folder" class="h-4 w-4 text-gray-400" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-medium text-gray-900 truncate">{{ $document->title }}</p>
                                        <p class="text-[12px] text-gray-400 truncate">{{ $document->file_name }}</p>
                                    </div>
                                </div>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="sm">
                                <span class="text-[13px] text-gray-600">{{ $document->category?->name ?? '—' }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="md">
                                <span class="text-[13px] text-gray-600">{{ $document->uploader?->name ?? '—' }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="md">
                                <span class="text-[12px] text-gray-400">
                                    @if($document->file_size)
                                        {{ number_format($document->file_size / 1024, 0) }} Ko
                                    @else
                                        —
                                    @endif
                                </span>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="sm">
                                <span class="text-[11px] font-medium text-gray-400">{{ $document->accreditation_level }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell :last="true">
                                <div x-data="{ open: false }" class="relative" @click.stop>
                                    <button @click="open = !open" class="rounded-md p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                                        <x-ui.icon name="ellipsis-horizontal" class="h-4 w-4" />
                                    </button>
                                    <div x-show="open" x-transition @click.outside="open = false" class="absolute right-0 top-full mt-1 w-44 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200 z-50">
                                        <a href="{{ route('library.show', $document) }}" class="flex items-center gap-x-2 px-3 py-2 text-[13px] text-gray-700 hover:bg-gray-50">
                                            <x-ui.icon name="folder" class="h-4 w-4 text-gray-400" />
                                            Consulter
                                        </a>
                                        <a href="{{ route('library.download', $document) }}" class="flex items-center gap-x-2 px-3 py-2 text-[13px] text-gray-700 hover:bg-gray-50">
                                            <x-ui.icon name="arrow-down-tray" class="h-4 w-4 text-gray-400" />
                                            Télécharger
                                        </a>
                                    </div>
                                </div>
                            </x-ui.table.cell>
                        </x-ui.table.row>
                    @endforeach
                </x-ui.table.body>
            </x-ui.table>

            <div class="mt-4">
                <x-ui.pagination :paginator="$documents" mode="livewire" />
            </div>
        @endif
    </div>
</div>
