@extends('layouts.app')

@section('title', 'Modifier ' . $report->code)

@section('content')
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('reports.index')">Rapports</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item :href="route('reports.show', $report)">{{ $report->code }}</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>Modifier</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    <div class="mt-4">
        <h1 class="text-lg font-semibold text-gray-900">Modifier le rapport</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">Modifier les informations et le contenu du rapport.</p>
    </div>

    <div class="mt-6">
        <livewire:admin.report-form :report="$report" />
    </div>
@endsection
