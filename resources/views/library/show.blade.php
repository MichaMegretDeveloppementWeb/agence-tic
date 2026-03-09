@extends('layouts.app')

@section('title', $document->title)

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('library.index')">Bibliothèque</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>{{ $document->title }}</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    <div class="mt-4 space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-x-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-100">
                    <x-ui.icon name="document-duplicate" class="h-6 w-6 text-gray-400" />
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">{{ $document->title }}</h1>
                    <p class="mt-0.5 text-[13px] text-gray-500">{{ $document->file_name }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-ui.button variant="secondary" :href="route('library.index')">
                    <x-ui.icon name="arrow-left" class="h-4 w-4" />
                    Retour
                </x-ui.button>
                <x-ui.button variant="secondary" :href="route('library.edit', $document)">
                    <x-ui.icon name="pencil-square" class="h-4 w-4" />
                    Modifier
                </x-ui.button>
                <x-ui.button :href="route('library.download', $document)">
                    <x-ui.icon name="arrow-down-tray" class="h-4 w-4" />
                    Télécharger
                </x-ui.button>
            </div>
        </div>

        {{-- Metadata bar --}}
        @php
            $fileExtension = strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION));
            $fileSize = number_format($document->file_size / 1024, 1);
        @endphp
        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-xl border border-gray-200 bg-white px-5 py-3">
            {{-- Category --}}
            <div class="flex items-center gap-x-1.5">
                <x-ui.icon name="squares-2x2" class="h-4 w-4 text-gray-400" />
                <span class="text-[13px] text-gray-600">{{ $document->category?->name ?? '—' }}</span>
            </div>

            <span class="text-gray-200">|</span>

            {{-- Status --}}
            <x-ui.badge :color="$document->status->badgeColor()" dot>{{ $document->status->label() }}</x-ui.badge>

            <span class="text-gray-200">|</span>

            {{-- Accreditation level --}}
            <div class="flex items-center gap-x-1.5">
                <x-ui.icon name="lock-closed" class="h-4 w-4 text-gray-400" />
                <span class="text-[13px] text-gray-600">Niveau {{ $document->accreditation_level }}</span>
            </div>

            <span class="text-gray-200">|</span>

            {{-- Uploader --}}
            <div class="flex items-center gap-x-1.5">
                <x-ui.icon name="user" class="h-4 w-4 text-gray-400" />
                <span class="text-[13px] text-gray-600">{{ $document->uploader?->name ?? '—' }}</span>
            </div>

            <span class="text-gray-200">|</span>

            {{-- File type + size --}}
            <div class="flex items-center gap-x-1.5">
                <x-ui.icon name="document-duplicate" class="h-4 w-4 text-gray-400" />
                <span class="text-[13px] text-gray-600">{{ $fileExtension }} · {{ $fileSize }} Ko</span>
            </div>

            <span class="text-gray-200">|</span>

            {{-- Date --}}
            <span class="text-[13px] text-gray-600">{{ $document->created_at->format('d/m/Y à H:i') }}</span>
        </div>


        {{-- Notes --}}
        @if(!empty($document->notes))
            <x-ui.card>
                <h2 class="text-[13px] font-semibold text-gray-900 mb-3">Notes</h2>
                <p class="text-[13px] text-gray-600 whitespace-pre-line">{{ $document->notes }}</p>
            </x-ui.card>
        @endif

        {{-- Associated report --}}
        @if($document->report)
            <x-ui.card>
                <h2 class="text-[13px] font-semibold text-gray-900 mb-3">Rapport associé</h2>
                <a href="{{ route('reports.show', $document->report) }}" class="group flex items-center gap-x-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 group-hover:bg-gray-200 transition-colors">
                        <x-ui.icon name="clipboard-document-list" class="h-4 w-4 text-gray-400" />
                    </div>
                    <div>
                        <p class="text-[13px] font-medium text-gray-900 group-hover:text-gray-600">{{ $document->report->code }}</p>
                        <p class="text-[12px] text-gray-400">{{ $document->report->title }}</p>
                    </div>
                </a>
            </x-ui.card>
        @endif


        {{-- File preview --}}
        @php
            $previewableTypes = [
                'application/pdf',
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/msword',
                'text/plain', 'text/csv', 'text/html', 'text/markdown',
                'application/rtf',
            ];
            $isPreviewable = in_array($document->mime_type, $previewableTypes)
                && \Illuminate\Support\Facades\Storage::disk('private')->exists($document->file_path);
            $isPdf = $document->mime_type === 'application/pdf';
            $isImage = str_starts_with($document->mime_type ?? '', 'image/');
            $isDocx = in_array($document->mime_type, [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/msword',
            ]);
            $isText = in_array($document->mime_type, [
                'text/plain', 'text/csv', 'text/html', 'text/markdown', 'application/rtf',
            ]);
        @endphp

        @if($isPreviewable)
            <x-ui.card :padding="false">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-[13px] font-semibold text-gray-900">Aperçu</h2>
                </div>
                <div class="p-4">
                    @if($isPdf)
                        <iframe src="{{ route('library.preview', $document) }}" class="w-full h-[600px] rounded-lg border border-gray-200"></iframe>
                    @elseif($isImage)
                        <img src="{{ route('library.preview', $document) }}" alt="{{ $document->title }}" class="max-w-full h-auto rounded-lg" />
                    @elseif($isDocx)
                        <div
                            x-data="docxPreview('{{ route('library.preview', $document) }}')"
                            class="rounded-lg border border-gray-200 bg-white"
                        >
                            <div x-show="loading" class="flex items-center justify-center py-16">
                                <div class="flex items-center gap-x-3 text-[13px] text-gray-400">
                                    <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Chargement du document...
                                </div>
                            </div>
                            <div x-show="error" class="flex items-center justify-center py-16">
                                <p class="text-[13px] text-red-500" x-text="error"></p>
                            </div>
                            <div
                                x-ref="docxContent"
                                x-show="!loading && !error"
                                class="prose prose-sm max-w-none max-h-[600px] overflow-auto p-6 text-[13px] text-gray-700 [&_table]:border-collapse [&_td]:border [&_td]:border-gray-200 [&_td]:px-3 [&_td]:py-1.5 [&_th]:border [&_th]:border-gray-200 [&_th]:bg-gray-50 [&_th]:px-3 [&_th]:py-1.5"
                            ></div>
                        </div>
                    @elseif($isText)
                        <div
                            x-data="textPreview('{{ route('library.preview', $document) }}')"
                            class="rounded-lg border border-gray-200 bg-white"
                        >
                            <div x-show="loading" class="flex items-center justify-center py-16">
                                <div class="flex items-center gap-x-3 text-[13px] text-gray-400">
                                    <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Chargement...
                                </div>
                            </div>
                            <pre
                                x-show="!loading && !error"
                                x-text="content"
                                class="max-h-[600px] overflow-auto p-6 text-[13px] text-gray-700 whitespace-pre-wrap"
                            ></pre>
                            <div x-show="error" class="flex items-center justify-center py-16">
                                <p class="text-[13px] text-red-500" x-text="error"></p>
                            </div>
                        </div>
                    @endif
                </div>
            </x-ui.card>
        @endif

    </div>
@endsection
