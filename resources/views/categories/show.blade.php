@extends('layouts.app')

@section('title', $category->name)

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('categories.index')">Catégories</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>{{ $category->name }}</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    <div class="mt-4 space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-x-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-100">
                    <x-ui.icon name="squares-2x2" class="h-6 w-6 text-gray-500" />
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="mt-0.5 text-[13px] text-gray-500">{{ $category->description }}</p>
                    @endif
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-x-2">
                <x-ui.button variant="secondary" :href="route('categories.index')">
                    <x-ui.icon name="arrow-left" class="h-4 w-4" />
                    Retour
                </x-ui.button>
                @if(auth()->user()->isDirectorG())
                    <x-ui.button variant="secondary" :href="route('categories.edit', $category)">
                        <x-ui.icon name="pencil-square" class="h-4 w-4" />
                        Modifier
                    </x-ui.button>
                @endif
            </div>
        </div>

        {{-- Stats bar --}}
        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-xl border border-gray-200 bg-white px-5 py-3">
            <div class="flex items-center gap-x-2">
                <x-ui.icon name="clipboard-document-list" class="h-4 w-4 text-gray-400" />
                <span class="text-[13px] text-gray-600">
                    <span class="font-medium text-gray-900">{{ $reports->count() }}</span> rapport{{ $reports->count() > 1 ? 's' : '' }}
                </span>
            </div>
            <div class="flex items-center gap-x-2">
                <x-ui.icon name="document-duplicate" class="h-4 w-4 text-gray-400" />
                <span class="text-[13px] text-gray-600">
                    <span class="font-medium text-gray-900">{{ $documents->count() }}</span> document{{ $documents->count() > 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        {{-- Rapports --}}
        @if($reports->isNotEmpty())
            <x-ui.card :padding="false">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-[13px] font-semibold text-gray-900">Rapports</h2>
                    <p class="mt-0.5 text-[12px] text-gray-400">{{ $reports->count() }} rapport(s) accessible(s) dans cette catégorie.</p>
                </div>
                <x-ui.table>
                    <x-ui.table.head>
                        <x-ui.table.header-cell :first="true">Code</x-ui.table.header-cell>
                        <x-ui.table.header-cell>Titre</x-ui.table.header-cell>
                        <x-ui.table.header-cell hidden="sm">Niveau de menace</x-ui.table.header-cell>
                        <x-ui.table.header-cell hidden="md">Statut</x-ui.table.header-cell>
                        <x-ui.table.header-cell :last="true">Accréditation</x-ui.table.header-cell>
                    </x-ui.table.head>
                    <x-ui.table.body>
                        @foreach($reports as $report)
                            <x-ui.table.row class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('reports.show', $report) }}'">
                                <x-ui.table.cell :first="true">
                                    <span class="text-[13px] font-medium text-gray-900">{{ $report->code }}</span>
                                </x-ui.table.cell>
                                <x-ui.table.cell>
                                    <span class="text-[13px] text-gray-600">{{ $report->title }}</span>
                                </x-ui.table.cell>
                                <x-ui.table.cell hidden="sm">
                                    <x-ui.badge :color="$report->threat_level->badgeColor()" dot>{{ $report->threat_level->label() }}</x-ui.badge>
                                </x-ui.table.cell>
                                <x-ui.table.cell hidden="md">
                                    <x-ui.badge :color="$report->status->badgeColor()">{{ $report->status->label() }}</x-ui.badge>
                                </x-ui.table.cell>
                                <x-ui.table.cell :last="true">
                                    <span class="text-[12px] text-gray-400">Niveau {{ $report->accreditation_level }}</span>
                                </x-ui.table.cell>
                            </x-ui.table.row>
                        @endforeach
                    </x-ui.table.body>
                </x-ui.table>
            </x-ui.card>
        @else
            <x-ui.empty-state icon="clipboard-document-list" title="Aucun rapport" description="Aucun rapport accessible dans cette catégorie." />
        @endif

        {{-- Documents --}}
        @if($documents->isNotEmpty())
            <x-ui.card :padding="false">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-[13px] font-semibold text-gray-900">Documents</h2>
                    <p class="mt-0.5 text-[12px] text-gray-400">{{ $documents->count() }} document(s) accessible(s) dans cette catégorie.</p>
                </div>
                <x-ui.table>
                    <x-ui.table.head>
                        <x-ui.table.header-cell :first="true">Titre</x-ui.table.header-cell>
                        <x-ui.table.header-cell hidden="sm">Fichier</x-ui.table.header-cell>
                        <x-ui.table.header-cell hidden="md">Statut</x-ui.table.header-cell>
                        <x-ui.table.header-cell :last="true">Accréditation</x-ui.table.header-cell>
                    </x-ui.table.head>
                    <x-ui.table.body>
                        @foreach($documents as $document)
                            <x-ui.table.row class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('library.show', $document) }}'">
                                <x-ui.table.cell :first="true">
                                    <span class="text-[13px] font-medium text-gray-900">{{ $document->title }}</span>
                                </x-ui.table.cell>
                                <x-ui.table.cell hidden="sm">
                                    <span class="text-[13px] text-gray-600">{{ $document->file_name }}</span>
                                </x-ui.table.cell>
                                <x-ui.table.cell hidden="md">
                                    <x-ui.badge :color="$document->status->badgeColor()">{{ $document->status->label() }}</x-ui.badge>
                                </x-ui.table.cell>
                                <x-ui.table.cell :last="true">
                                    <span class="text-[12px] text-gray-400">Niveau {{ $document->accreditation_level }}</span>
                                </x-ui.table.cell>
                            </x-ui.table.row>
                        @endforeach
                    </x-ui.table.body>
                </x-ui.table>
            </x-ui.card>
        @else
            <x-ui.empty-state icon="document-duplicate" title="Aucun document" description="Aucun document accessible dans cette catégorie." />
        @endif
    </div>
@endsection
