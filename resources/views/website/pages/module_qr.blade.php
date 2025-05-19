@extends('website.layouts.single_price')

@section('title', $data['title'] . ' - Avtomato')

@section('meta')
    <meta name="title" content="QR Pay – O'zbekistonda MIR kartasi bilan to'lov">
    <meta name="description" content="Rossiya MIR kartasi bilan O'zbekistonda to'lovni amalga oshirish imkoniyati.">
    <meta property="og:title" content="QR Pay – O'zbekistonda MIR kartasi bilan to'lov">
    <meta property="og:description"
          content="Rossiya MIR kartasi bilan O'zbekistonda to'lovni amalga oshirish imkoniyati.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ current_route() }}">
    <meta property="og:image" content="{{ asset('/cover.webp') }}">
    <meta property="og:image:type" content="image/webp">
    <meta name="twitter:card" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:title" content="QR Pay – O'zbekistonda MIR kartasi bilan to'lov">
    <meta name="twitter:description"
          content="Rossiya MIR kartasi bilan O'zbekistonda to'lovni amalga oshirish imkoniyati.">
    <meta name="twitter:image" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:image:type" content="image/webp">
@endsection

@section('price_list')
    @if (!empty($data['prices']))
        @include('website.components.price_list', ['list' => $data['prices']])
    @endif
@endsection