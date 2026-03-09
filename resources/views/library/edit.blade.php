@extends('layouts.app')

@section('title', 'Modifier ' . $document->title)

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('library.index')">Bibliothèque</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item :href="route('library.show', $document)">{{ $document->title }}</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>Modifier</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    {{-- Header --}}
    <div class="mt-4">
        <h1 class="text-lg font-semibold text-gray-900">Modifier le document</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">Modifier les informations et le fichier du document.</p>
    </div>

    {{-- Form --}}
    <div class="mt-6">
        <livewire:admin.document-form :document="$document" />
    </div>
@endsection
