@extends('layouts.app')

@section('title', 'Nouvel agent')

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('admin.agents.index')">Agents</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>Nouvel agent</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    {{-- Header --}}
    <div class="mt-4">
        <h1 class="text-lg font-semibold text-gray-900">Nouvel agent</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">Créer un nouveau compte agent pour l'Agence TIC.</p>
    </div>

    {{-- Form --}}
    <div class="mt-6">
        <livewire:admin.agent-form />
    </div>
@endsection
