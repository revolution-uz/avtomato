@extends('website.layouts.single_price')

@section('title', $data['title'] . ' - Avtomato')

@section("meta")
    <meta name="title" content="Internet Do'kon Yaratish – Onlayn savdo platformasi">
    <meta name="description" content="Oson va qulay onlayn do'kon yaratish imkoniyati.">
    <meta property="og:title" content="Internet Do'kon Yaratish – Onlayn savdo platformasi">
    <meta property="og:description" content="Oson va qulay onlayn do'kon yaratish imkoniyati.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ current_route() }}">
    <meta property="og:image" content="{{ asset('/cover.webp') }}">
    <meta property="og:image:type" content="image/webp">
    <meta name="twitter:card" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:title" content="Internet Do'kon Yaratish – Onlayn savdo platformasi">
    <meta name="twitter:description" content="Oson va qulay onlayn do'kon yaratish imkoniyati.">
    <meta name="twitter:image" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:image:" content="image/webp">
@endsection

@section('price_list')
    @if (!empty($data['prices']))
        @include('website.components.price_list', ['list' => $data['prices']])
    @endif
@endsection