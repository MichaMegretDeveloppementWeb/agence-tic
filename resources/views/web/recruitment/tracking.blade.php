@extends('layouts.web')

@section('title', 'Suivi de candidature')

@section('content')
    <div class="bg-gray-50 px-4 py-16 sm:px-6 sm:py-20">
        <div class="mx-auto max-w-lg">

            {{-- En-tete --}}
            <div class="mb-8 text-center">
                <h1 class="text-lg font-semibold text-gray-900">Suivi de candidature</h1>
                <p class="mt-2 text-[13px] text-gray-500">
                    Entrez votre code de suivi pour consulter l'état de votre candidature.
                </p>
            </div>

            {{-- Composant Livewire --}}
            <livewire:web.application-tracker />

            {{-- Lien retour --}}
            <div class="mt-6 text-center">
                <a href="{{ route('recruitment') }}" class="text-[13px] text-gray-500 hover:text-gray-700">
                    Vous n'avez pas encore postule ?
                    <span class="font-medium text-gray-900 hover:text-gray-700">Soumettre une candidature</span>
                </a>
            </div>

        </div>
    </div>
@endsection
