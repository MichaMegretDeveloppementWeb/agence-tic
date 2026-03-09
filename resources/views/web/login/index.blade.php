@extends('layouts.web')

@section('title', 'Connexion')

@section('content')
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">

            {{-- En-tête --}}
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-900">
                    <x-ui.icon name="lock-closed" class="h-5 w-5 text-white" />
                </div>
                <h1 class="text-lg font-semibold text-gray-900">Zone d'accès restreint</h1>
                <p class="mt-1 text-[13px] text-gray-500">Identifiez-vous pour accéder à l'espace sécurisé de l'Agence TIC.</p>
            </div>

            {{-- Formulaire Livewire --}}
            <x-ui.card>
                <livewire:auth.login-form />
            </x-ui.card>

            {{-- Lien recrutement --}}
            <p class="mt-6 text-center text-[12px] text-gray-400">
                Vous n'êtes pas agent ?
                <a href="{{ route('recruitment') }}" class="font-medium text-gray-600 hover:text-gray-900">Postuler</a>
            </p>

        </div>
    </div>
@endsection
