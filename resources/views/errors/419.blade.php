@extends('errors.layout')

@section('title', 'Session expirée')
@section('code', '419')
@section('heading', 'Session expirée')
@section('message', 'Votre session a expiré. Vous allez être redirigé vers la page de connexion.')

@section('icon-bg', 'bg-amber-50')
@section('icon')
    {{-- Heroicon: clock --}}
    <svg class="h-7 w-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
@endsection

@section('action')
    <a href="{{ url('/login') }}" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-1.5 text-[13px] font-medium text-white transition-colors hover:bg-gray-800">
        Se connecter
    </a>
    <script>
        setTimeout(function () { window.location.href = '{{ url('/login') }}'; }, 2000);
    </script>
@endsection
