@extends('layouts.app')

@section('title', 'Modifier ' . $category->name)

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('categories.index')">Catégories</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item :href="route('categories.show', $category)">{{ $category->name }}</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>Modifier</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    {{-- Header --}}
    <div class="mt-4">
        <h1 class="text-lg font-semibold text-gray-900">Modifier la catégorie</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">Modifiez les informations de la catégorie.</p>
    </div>

    {{-- Form --}}
    <div class="mt-6">
        <livewire:admin.category-form :category="$category" />
    </div>
@endsection
