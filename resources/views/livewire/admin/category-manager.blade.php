<div>
    {{-- En-tête de page --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Catégories</h1>
            <p class="mt-0.5 text-[13px] text-gray-500">
                Organisez les rapports et documents par catégorie.
            </p>
        </div>
        @if($isDirector)
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button :href="route('categories.create')">
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    Nouvelle catégorie
                </x-ui.button>
            </div>
        @endif
    </div>

    {{-- Recherche + per-page --}}
    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-xs">
            <x-ui.icon name="magnifying-glass" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher une catégorie..." class="w-full rounded-lg border-0 bg-white py-1.5 pl-8 pr-3 text-[13px] text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-900" />
        </div>
        <x-per-page />
    </div>

    {{-- Liste des catégories --}}
    <div class="mt-4">
        @if($categories->isEmpty())
            <x-ui.empty-state
                icon="folder"
                title="Aucune catégorie"
                description="Créez une première catégorie pour organiser vos rapports et documents."
            >
                @if($isDirector)
                    <x-ui.button :href="route('categories.create')">
                        <x-ui.icon name="plus" class="h-4 w-4" />
                        Nouvelle catégorie
                    </x-ui.button>
                @endif
            </x-ui.empty-state>
        @else
            <x-ui.table>
                <x-ui.table.head>
                    <x-ui.table.header-cell :first="true">Nom</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="sm">Description</x-ui.table.header-cell>
                    <x-ui.table.header-cell align="center">Rapports</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="sm" align="center" :last="true">Documents</x-ui.table.header-cell>
                </x-ui.table.head>
                <x-ui.table.body>
                    @foreach($categories as $category)
                        <x-ui.table.row class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('categories.show', $category) }}'">
                            <x-ui.table.cell :first="true">
                                <span class="text-[13px] font-medium text-gray-900">{{ $category->name }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="sm">
                                <span class="text-[13px] text-gray-600">{{ $category->description ? Str::limit($category->description, 60) : '—' }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell align="center">
                                <span class="text-[13px] text-gray-600">{{ $category->reports_count }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="sm" align="center" :last="true">
                                <span class="text-[13px] text-gray-600">{{ $category->documents_count }}</span>
                            </x-ui.table.cell>
                        </x-ui.table.row>
                    @endforeach
                </x-ui.table.body>
            </x-ui.table>
        @endif

        @if($categories->hasPages())
            <div class="mt-4">
                <x-ui.pagination :paginator="$categories" mode="livewire" />
            </div>
        @endif
    </div>
</div>
