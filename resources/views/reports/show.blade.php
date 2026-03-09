@extends('layouts.app')

@section('title', $report->code . ' — ' . $report->title)

@section('content')

    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('reports.index')">Rapports</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>{{ $report->code }}</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    <div class="mt-4 space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-x-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-100">
                    <x-ui.icon name="clipboard-document-list" class="h-6 w-6 text-gray-500" />
                </div>
                <div>
                    <div class="flex items-center gap-x-3">
                        <h1 class="text-lg font-semibold text-gray-900">{{ $report->code }}</h1>
                        <x-ui.badge :color="$report->status->badgeColor()" dot>{{ $report->status->label() }}</x-ui.badge>
                    </div>
                    <p class="mt-0.5 text-[13px] text-gray-500">{{ $report->title }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button variant="secondary" :href="route('reports.index')">
                    <x-ui.icon name="arrow-left" class="h-4 w-4" />
                    Retour
                </x-ui.button>
                <x-ui.button variant="secondary" :href="route('library.create', ['report' => $report->id])">
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    Ajouter un document
                </x-ui.button>
                <x-ui.button variant="secondary" :href="route('reports.edit', $report)">
                    <x-ui.icon name="pencil-square" class="h-4 w-4" />
                    Modifier
                </x-ui.button>
                <x-ui.button variant="secondary" :href="route('reports.create', ['duplicate' => $report->id])">
                    <x-ui.icon name="document-duplicate" class="h-4 w-4" />
                    Dupliquer
                </x-ui.button>
            </div>
        </div>

        {{-- Metadata bar --}}
        @php
            $threatIconColor = match ($report->threat_level) {
                App\Enums\ThreatLevel::Low => 'text-emerald-500',
                App\Enums\ThreatLevel::Moderate => 'text-emerald-500',
                App\Enums\ThreatLevel::High => 'text-amber-500',
                App\Enums\ThreatLevel::Critical => 'text-red-500',
                App\Enums\ThreatLevel::Extreme => 'text-red-500',
            };
        @endphp
        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-xl border border-gray-200 bg-white px-5 py-3">
            {{-- Category --}}
            @if($report->category)
                <div class="flex items-center gap-x-1.5">
                    <x-ui.icon name="squares-2x2" class="h-4 w-4 text-gray-400" />
                    <span class="text-[13px] text-gray-600">{{ $report->category->name }}</span>
                </div>
            @endif

            {{-- Threat level --}}
            <div class="flex items-center gap-x-1.5">
                <x-ui.icon name="exclamation-triangle" class="h-4 w-4 {{ $threatIconColor }}" />
                <span class="text-[13px] text-gray-600">Menace : {{ $report->threat_level->label() }}</span>
            </div>

            {{-- Accreditation level --}}
            <div class="flex items-center gap-x-1.5">
                <x-ui.icon name="lock-closed" class="h-4 w-4 text-gray-400" />
                <span class="text-[13px] text-gray-600">Niveau {{ $report->accreditation_level }}</span>
            </div>

            {{-- Created date --}}
            <div class="flex items-center gap-x-1.5">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v9.75" />
                </svg>
                <span class="text-[13px] text-gray-600">Créé le {{ $report->created_at->format('d/m/Y') }}</span>
            </div>

            {{-- Updated date (only if different from created) --}}
            @if($report->updated_at->format('d/m/Y H:i') !== $report->created_at->format('d/m/Y H:i'))
                <div class="flex items-center gap-x-1.5">
                    <x-ui.icon name="arrow-path" class="h-4 w-4 text-gray-400" />
                    <span class="text-[13px] text-gray-600">MAJ le {{ $report->updated_at->format('d/m/Y') }}</span>
                </div>
            @endif
        </div>

        {{-- Description --}}
        @if($report->description)
            <x-ui.card>
                <h2 class="text-[13px] font-semibold text-gray-900 mb-3">Description</h2>
                <div class="text-[13px] text-gray-600 leading-relaxed whitespace-pre-line">{{ $report->description }}</div>
            </x-ui.card>
        @endif

        {{-- Procedures --}}
        @if($report->procedures)
            <x-ui.card>
                <h2 class="text-[13px] font-semibold text-gray-900 mb-3">Procédures de confinement</h2>
                <div class="text-[13px] text-gray-600 leading-relaxed whitespace-pre-line">{{ $report->procedures }}</div>
            </x-ui.card>
        @endif

        {{-- Notes --}}
        @if($report->notes)
            <x-ui.card>
                <h2 class="text-[13px] font-semibold text-gray-900 mb-3">Notes</h2>
                <div class="text-[13px] text-gray-600 leading-relaxed whitespace-pre-line">{{ $report->notes }}</div>
            </x-ui.card>
        @endif

        {{-- Documents --}}
        @php
            $accessibleDocuments = $report->documents->filter(function ($document) use ($user) {
                return $user->canAccess($document->accreditation_level, App\Models\Document::class, $document->id);
            });
        @endphp

        @if($accessibleDocuments->isNotEmpty())
            <x-ui.card :padding="false">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-[13px] font-semibold text-gray-900">Documents associés</h2>
                    <p class="mt-0.5 text-[12px] text-gray-400">{{ $accessibleDocuments->count() }} document(s) lié(s) à ce rapport.</p>
                </div>
                <div class="overflow-x-auto scrollbar-thin">
                    <x-ui.table>
                        <x-ui.table.head>
                            <x-ui.table.header-cell :first="true">Document</x-ui.table.header-cell>
                            <x-ui.table.header-cell hidden="sm">Catégorie</x-ui.table.header-cell>
                            <x-ui.table.header-cell hidden="md">Taille</x-ui.table.header-cell>
                            <x-ui.table.header-cell :last="true"></x-ui.table.header-cell>
                        </x-ui.table.head>
                        <x-ui.table.body>
                            @foreach($accessibleDocuments as $document)
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
                                        <span class="text-[12px] text-gray-400">
                                            @if($document->file_size)
                                                {{ number_format($document->file_size / 1024, 0) }} Ko
                                            @else
                                                —
                                            @endif
                                        </span>
                                    </x-ui.table.cell>
                                    <x-ui.table.cell :last="true">
                                        <a href="{{ route('library.download', $document) }}" class="inline-flex items-center gap-x-1 text-[13px] text-gray-600 hover:text-gray-900" @click.stop>
                                            <x-ui.icon name="arrow-down-tray" class="h-4 w-4" />
                                        </a>
                                    </x-ui.table.cell>
                                </x-ui.table.row>
                            @endforeach
                        </x-ui.table.body>
                    </x-ui.table>
                </div>
            </x-ui.card>
        @endif

        {{-- Activity timeline --}}
        @if($report->activityEntries->isNotEmpty())
            <x-ui.card>
                <h2 class="text-[13px] font-semibold text-gray-900 mb-4">Historique</h2>
                <x-ui.timeline>
                    @foreach($report->activityEntries as $entry)
                        <x-ui.timeline.item
                            :title="'<span class=\'font-medium text-gray-900\'>' . e($entry->user?->name ?? 'Système') . '</span> — ' . e($entry->message)"
                            :date="$entry->created_at->diffForHumans()"
                            :icon="$entry->event_type === 'created' ? 'plus' : ($entry->event_type === 'updated' ? 'pencil-square' : 'information-circle')"
                            :color="$entry->event_type === 'created' ? 'emerald' : ($entry->event_type === 'updated' ? 'blue' : 'gray')"
                        />
                    @endforeach
                </x-ui.timeline>
            </x-ui.card>
        @endif

        {{-- Comments --}}
        <x-ui.card>
            @livewire('agent.report-comments', ['report' => $report])
        </x-ui.card>

    </div>

@endsection
