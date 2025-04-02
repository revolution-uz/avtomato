@extends('website.layouts.single_price')

@section('title', $data['title'] . ' - Avtomato')

@section('meta')
    <meta name="title" content="Nasiya Savdo – Avtomatlashtirilgan to'lov tizimi">
    <meta name="description"
          content="Nasiya savdoni avtomatlashtiring va mijozlaringiz uchun qulay to'lov variantlarini taqdim eting.">
    <meta property="og:title" content="Nasiya Savdo – Avtomatlashtirilgan to'lov tizimi">
    <meta property="og:description"
          content="Nasiya savdoni avtomatlashtiring va mijozlaringiz uchun qulay to'lov variantlarini taqdim eting.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ current_route() }}">
    <meta property="og:image" content="{{ asset('/cover.webp') }}">
    <meta property="og:image:type" content="image/webp">
    <meta name="twitter:card" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:title" content="Nasiya Savdo – Avtomatlashtirilgan to'lov tizimi">
    <meta name="twitter:description"
          content="Nasiya savdoni avtomatlashtiring va mijozlaringiz uchun qulay to'lov variantlarini taqdim eting.">
    <meta name="twitter:image" content="{{ asset('/cover.webp') }}">
    <meta name="twitter:image:type" content="image/webp">
@endsection

@section('price_list')
    @if (!empty($data['prices']))
        @include('website.components.price_list', ['list' => $data['prices']])
    @endif
@endsection