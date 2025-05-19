@extends('website.layouts.single_price')

@section('title', $data['title'] . ' - Avtomato')

@section('meta')
    <meta name="title" content="Marketing – To'lovlarni boshqarish va rejalashtirish">
    <meta name="description" content="To'lovlarni avtomatik tarzda rejalashtirish, kuzatish va boshqarish.">
    <meta property="og:title" content="Marketing – To'lovlarni boshqarish va rejalashtirish">
    <meta property="og:description" content="To'lovlarni avtomatik tarzda rejalashtirish, kuzatish va boshqarish.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ current_route() }}">
    <meta property="og:image" content="{{ asset('/cover.webp') }}">
    <meta property="og:image:type" content="image/webp">
    <meta name="twitter:card" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:title" content="Marketing – To'lovlarni boshqarish va rejalashtirish">
    <meta name="twitter:description" content="To'lovlarni avtomatik tarzda rejalashtirish, kuzatish va boshqarish.">
    <meta name="twitter:image" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:image:type" content="image/webp">
@endsection

@section('price_list')
    @if (!empty($data['prices']))
        @include('website.components.price_list', ['list' => $data['prices']])
    @endif
@endsection