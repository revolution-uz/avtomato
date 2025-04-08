@extends('website.layouts.single_price')

@section('title', $data['title'] . ' - Avtomato')

@section('meta')
    <meta name="title" content="Qo'shimcha Xizmatlar – Avtomatik to'lov yechish va mablag'ni boshqarish">
    <meta name="description"
          content="Mijoz kartasida mablag' bo'lmasa, PINFL orqali avtomatik pul yechib olish imkoniyati.">
    <meta property="og:title" content="Qo'shimcha Xizmatlar – Avtomatik to'lov yechish va mablag'ni boshqarish">
    <meta property="og:description"
          content="Mijoz kartasida mablag' bo'lmasa, PINFL orqali avtomatik pul yechib olish imkoniyati.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ current_route() }}">
    <meta property="og:image" content="{{ asset('/cover.webp') }}">
    <meta property="og:image:type" content="image/webp">
    <meta name="twitter:card" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:title" content="Qo'shimcha Xizmatlar – Avtomatik to'lov yechish va mablag'ni boshqarish">
    <meta name="twitter:description"
          content="Mijoz kartasida mablag' bo'lmasa, PINFL orqali avtomatik pul yechib olish imkoniyati.">
    <meta name="twitter:image" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:image:type" content="image/webp">
@endsection

@section('price_list')
    @if (!empty($data['prices']))
        @include('website.components.price_table', ['list' => $data['prices']])
    @endif
@endsection