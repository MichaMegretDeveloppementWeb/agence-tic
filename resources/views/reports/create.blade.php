@extends('layouts.app')

@section('title', 'Nouveau rapport')

@section('content')
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('reports.index')">Rapports</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>Nouveau rapport</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    <div class="mt-4">
        <h1 class="text-lg font-semibold text-gray-900">Nouveau rapport</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">Créez un nouveau rapport TIC.</p>
    </div>

    <div class="mt-6">
        <livewire:admin.report-form />
    </div>
@endsection
