@extends('website.layouts.single_price')

@section('title', $data['title'] . ' - Avtomato')

@section('meta')
    <meta name="title" content="Loyalty – Mijozlar uchun analitika va statistikalar">
    <meta name="description" content="Mijozlar haqida real vaqt rejimida ma'lumotlar, kirim-chiqimlar va foydalar.">
    <meta property="og:title" content="Loyalty – Mijozlar uchun analitika va statistikalar">
    <meta property="og:description"
          content="Mijozlar haqida real vaqt rejimida ma'lumotlar, kirim-chiqimlar va foydalar.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ current_route() }}">
    <meta property="og:image" content="{{ asset('/cover.webp') }}">
    <meta property="og:image:type" content="image/webp">
    <meta name="twitter:card" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:title" content="Loyalty – Mijozlar uchun analitika va statistikalar">
    <meta name="twitter:description"
          content="Mijozlar haqida real vaqt rejimida ma'lumotlar, kirim-chiqimlar va foydalar.">
    <meta name="twitter:image" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:image:type" content="image/webp">
@endsection

@section('price_list')
    @if (!empty($data['prices']))
        @include('website.components.price_list', ['list' => $data['prices']])
    @endif
@endsection