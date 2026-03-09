@extends('layouts.web')

@section('title', 'Recrutement')

@section('content')
    <div class="bg-gray-50 px-4 py-16 sm:px-6 sm:py-20">
        <div class="mx-auto max-w-lg">

            {{-- En-tête --}}
            <div class="mb-8 text-center">
                <h1 class="text-lg font-semibold text-gray-900">Rejoindre l'Agence TIC</h1>
                <p class="mt-2 text-[13px] text-gray-500">
                    L'Agence recrute des individus déterminés et discrets.
                    Soumettez votre candidature — le Directeur G examinera personnellement chaque dossier.
                </p>
            </div>

            {{-- Formulaire Livewire --}}
            <livewire:web.application-form />

            {{-- Lien suivi --}}
            <div class="mt-6 text-center">
                <a href="{{ route('recruitment.tracking') }}" class="text-[13px] text-gray-500 hover:text-gray-700">
                    Vous avez deja postule ?
                    <span class="font-medium text-gray-900 hover:text-gray-700">Suivez votre candidature</span>
                </a>
            </div>

        </div>
    </div>
@endsection
