@extends('layouts.app')

@section('title', 'Modifier ' . $agent->agent_code)

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('admin.agents.index')">Agents</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item :href="route('admin.agents.show', $agent)">{{ $agent->agent_code }}</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>Modifier</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    {{-- Header --}}
    <div class="mt-4">
        <h1 class="text-lg font-semibold text-gray-900">Modifier {{ $agent->name }}</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">Modifier les informations et le niveau d'accréditation de l'agent.</p>
    </div>

    {{-- Form --}}
    <div class="mt-6">
        <livewire:admin.agent-form :agent="$agent" />
    </div>
@endsection
