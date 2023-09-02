@php use App\Helpers\Helpers; @endphp
@isset($pageConfigs)
    {!! Helpers::updatePageConfig($pageConfigs) !!}
@endisset
@php
    $configData = Helpers::appClasses();

    /* Display elements */
    $customizerHidden = ($customizerHidden ?? '');

@endphp

@extends('layouts/commonMaster' )

@section('layoutContent')

    <!-- Content -->
    @yield('content')
    <!--/ Content -->

@endsection
