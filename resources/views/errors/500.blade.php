@extends('errors.layout')

@section('title', 'Erreur serveur')
@section('code', '500')
@section('heading', 'Erreur serveur')
@section('message', 'Une erreur inattendue s\'est produite. Notre équipe a été notifiée. Veuillez réessayer dans quelques instants.')

@section('icon-bg', 'bg-red-50')
@section('icon')
    {{-- Heroicon: exclamation-triangle --}}
    <svg class="h-7 w-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
    </svg>
@endsection

@section('action')
    <a href="/" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-1.5 text-[13px] font-medium text-white transition-colors hover:bg-gray-800">
        Retour à l'accueil
    </a>
@endsection
