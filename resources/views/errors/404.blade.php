@extends('errors.layout')

@section('title', 'Page introuvable')
@section('code', '404')
@section('heading', 'Page introuvable')
@section('message', 'La page que vous recherchez n\'existe pas ou a été déplacée. Vérifiez l\'adresse ou retournez à l\'accueil.')

@section('icon-bg', 'bg-gray-100')
@section('icon')
    {{-- Heroicon: magnifying-glass --}}
    <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
    </svg>
@endsection

@section('action')
    <a href="/" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-1.5 text-[13px] font-medium text-white transition-colors hover:bg-gray-800">
        Retour à l'accueil
    </a>
@endsection
