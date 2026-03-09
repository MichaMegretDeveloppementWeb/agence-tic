@extends('layouts.app')

@section('title', 'Nouveau rappel')

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('reminders.index')">Rappels</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>Nouveau rappel</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    {{-- Header --}}
    <div class="mt-4">
        <h1 class="text-lg font-semibold text-gray-900">Nouveau rappel</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">Créez un nouveau rappel.</p>
    </div>

    {{-- Form --}}
    <div class="mt-6">
        <livewire:agent.reminder-form />
    </div>
@endsection
