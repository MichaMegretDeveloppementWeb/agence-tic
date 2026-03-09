@extends('errors.layout')

@section('title', 'Trop de requêtes')
@section('code', '429')
@section('heading', 'Trop de requêtes')
@section('message', 'Vous avez effectué trop de requêtes en peu de temps. Veuillez patienter quelques instants avant de réessayer.')

@section('icon-bg', 'bg-amber-50')
@section('icon')
    {{-- Heroicon: bolt --}}
    <svg class="h-7 w-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
    </svg>
@endsection

@section('action')
    <a href="/" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-1.5 text-[13px] font-medium text-white transition-colors hover:bg-gray-800">
        Retour à l'accueil
    </a>
@endsection
