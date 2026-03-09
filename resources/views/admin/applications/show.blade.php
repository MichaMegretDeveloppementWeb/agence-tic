@extends('layouts.app')

@section('title', $application->name)

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('admin.applications.index')">Candidatures</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>{{ $application->name }}</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    {{-- Header --}}
    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-x-2">
                <h1 class="text-lg font-semibold text-gray-900">{{ $application->name }}</h1>
                <x-ui.badge :color="$application->status->badgeColor()" dot>{{ $application->status->label() }}</x-ui.badge>
            </div>
            <p class="mt-0.5 text-[13px] text-gray-500">{{ $application->email }}</p>
        </div>
        <div class="flex items-center gap-x-2" x-data>
            <x-ui.button variant="secondary" :href="route('admin.applications.index')">Retour</x-ui.button>
            @if($application->status === \App\Enums\ApplicationStatus::Pending)
                <x-ui.button variant="danger" @click="$dispatch('open-modal', 'reject-application')">Refuser</x-ui.button>
                <x-ui.button @click="$dispatch('open-modal', 'accept-application')">Accepter</x-ui.button>
            @endif
        </div>
    </div>

    {{-- Content grid --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left column (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Motivation --}}
            <x-ui.card>
                <h2 class="text-[13px] font-semibold text-gray-900 mb-3">Motivation</h2>
                <p class="text-[13px] text-gray-600 whitespace-pre-line">{{ $application->motivation ?? '—' }}</p>
            </x-ui.card>

            {{-- Expérience --}}
            <x-ui.card>
                <h2 class="text-[13px] font-semibold text-gray-900 mb-3">Expérience</h2>
                <p class="text-[13px] text-gray-600 whitespace-pre-line">{{ $application->experience ?? '—' }}</p>
            </x-ui.card>
        </div>

        {{-- Right column (1/3) --}}
        <div class="space-y-6">
            <x-ui.card>
                <h2 class="text-[13px] font-semibold text-gray-900 mb-3">Informations</h2>
                <x-ui.description-list variant="stacked">
                    <x-ui.description-list.item label="Nom" variant="stacked">{{ $application->name }}</x-ui.description-list.item>
                    <x-ui.description-list.item label="E-mail" variant="stacked">{{ $application->email }}</x-ui.description-list.item>
                    <x-ui.description-list.item label="Statut" variant="stacked">
                        <x-ui.badge :color="$application->status->badgeColor()" dot>{{ $application->status->label() }}</x-ui.badge>
                    </x-ui.description-list.item>
                    <x-ui.description-list.item label="Date de candidature" variant="stacked">{{ $application->created_at->format('d/m/Y à H:i') }}</x-ui.description-list.item>
                </x-ui.description-list>
            </x-ui.card>
        </div>
    </div>

    @if($application->status === \App\Enums\ApplicationStatus::Pending)
        {{-- Modal : Accepter la candidature --}}
        <x-ui.modal name="accept-application" variant="content" title="Accepter la candidature">
            <livewire:admin.accept-application :application="$application" />
        </x-ui.modal>

        {{-- Modal : Refuser la candidature --}}
        <x-ui.modal name="reject-application" variant="content" title="Refuser cette candidature ?">
            <livewire:admin.reject-application :application="$application" />
        </x-ui.modal>
    @endif
@endsection
