@extends('website.pages.single_price')

@section('title', $data['title'] . ' - Avtomato')

@section('price_list')
    @if (!empty($data['prices']))
        @include('website.components.price_list', ['list' => $data['prices']])
    @endif
@endsection