@php use App\Helpers\Helpers; @endphp
@isset($pageConfigs)
    {!! Helpers::updatePageConfig($pageConfigs) !!}
@endisset
@php
    $configData = Helpers::appClasses();

    /* Display elements */
    $customizerHidden = ($customizerHidden ?? '');

@endphp

@extends('layouts/baseLayout')

@section('content')

    <!-- Content -->
    <main>
        {{ $slot }}
    </main>
    <!--/ Content -->

@endsection
