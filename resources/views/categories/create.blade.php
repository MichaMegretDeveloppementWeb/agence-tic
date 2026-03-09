@extends('layouts.app')

@section('title', 'Nouvelle catégorie')

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('categories.index')">Catégories</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>Nouvelle catégorie</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    {{-- Header --}}
    <div class="mt-4">
        <h1 class="text-lg font-semibold text-gray-900">Nouvelle catégorie</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">Créez une catégorie pour organiser rapports et documents.</p>
    </div>

    {{-- Form --}}
    <div class="mt-6">
        <livewire:admin.category-form />
    </div>
@endsection
