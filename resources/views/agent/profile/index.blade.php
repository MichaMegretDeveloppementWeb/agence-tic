@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
    {{-- Header --}}
    <div>
        <h1 class="text-lg font-semibold text-gray-900">Mon profil</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">Gérez vos informations personnelles et votre mot de passe.</p>
    </div>

    <div class="mt-6 space-y-8">
        {{-- Section 1: Photo de profil --}}
        <div>
            <div class="mb-4">
                <h2 class="text-[13px] font-semibold text-gray-900">Photo de profil</h2>
                <p class="mt-1 text-[13px] text-gray-500">Votre photo sera visible par les autres membres de l'agence.</p>
            </div>
            <x-ui.card>
                <livewire:agent.avatar-upload />
            </x-ui.card>
        </div>

        {{-- Section 2: Informations personnelles --}}
        <div>
            <div class="mb-4">
                <h2 class="text-[13px] font-semibold text-gray-900">Informations personnelles</h2>
                <p class="mt-1 text-[13px] text-gray-500">Mettez à jour votre nom et votre adresse e-mail.</p>
            </div>
            <x-ui.card>
                <livewire:agent.profile-form />
            </x-ui.card>
        </div>

        {{-- Section 3: Mot de passe --}}
        <div>
            <div class="mb-4">
                <h2 class="text-[13px] font-semibold text-gray-900">Mot de passe</h2>
                <p class="mt-1 text-[13px] text-gray-500">Assurez-vous d'utiliser un mot de passe long et sécurisé.</p>
            </div>
            <x-ui.card>
                <livewire:agent.password-form />
            </x-ui.card>
        </div>
    </div>
@endsection
