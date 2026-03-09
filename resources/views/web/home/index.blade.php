@extends('layouts.web')

@section('title', 'Accueil')

@section('content')

    {{-- Hero --}}
    @include('web.home.partials.hero')

    {{-- Mission --}}
    @include('web.home.partials.mission')

    {{-- CTA --}}
    @include('web.home.partials.cta')

@endsection
