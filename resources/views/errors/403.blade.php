@extends('errors.layout')

@section('title', 'Accès interdit')
@section('code', '403')
@section('heading', 'Accès interdit')
@section('message', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette ressource. Si vous pensez qu\'il s\'agit d\'une erreur, contactez votre administrateur.')

@section('icon-bg', 'bg-amber-50')
@section('icon')
    {{-- Heroicon: lock-closed --}}
    <svg class="h-7 w-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
    </svg>
@endsection

@section('action')
    <a href="/" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-1.5 text-[13px] font-medium text-white transition-colors hover:bg-gray-800">
        Retour à l'accueil
    </a>
@endsection
