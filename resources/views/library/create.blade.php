@extends('layouts.app')

@section('title', 'Nouveau document')

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('library.index')">Bibliothèque</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>Nouveau document</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    {{-- Header --}}
    <div class="mt-4">
        <h1 class="text-lg font-semibold text-gray-900">Nouveau document</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">Ajouter un nouveau document à la bibliothèque classifiée.</p>
    </div>

    {{-- Form --}}
    <div class="mt-6">
        <livewire:admin.document-form />
    </div>
@endsection
