@extends('errors.layout')

@section('title', 'Maintenance en cours')
@section('code', '503')
@section('heading', 'Maintenance en cours')
@section('message', 'L\'application est temporairement indisponible pour une opération de maintenance planifiée. Nous serons de retour très rapidement. Merci de votre patience.')

@section('icon-bg', 'bg-blue-50')
@section('icon')
    {{-- Heroicon: arrow-path --}}
    <svg class="h-7 w-7 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182M2.985 19.644l3.181-3.182" />
    </svg>
@endsection

@section('action')
    <button onclick="window.location.reload()" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-1.5 text-[13px] font-medium text-white transition-colors hover:bg-gray-800">
        Rafraîchir la page
    </button>
@endsection
