@php
$configData = \App\Helpers\Helpers::appClasses();
$isNavbar = false;
@endphp

@extends('template-layouts/layoutMaster')

@section('title', 'Without navbar - Layouts')

@section('content')

<!-- Layout Demo -->
<div class="layout-demo-wrapper">
  <div class="layout-demo-placeholder">
    <img src="{{asset('assets/img/template-layouts/layout-without-navbar-'.$configData['style'].'.png')}}" class="img-fluid" alt="Layout without navbar" data-app-light-img="template-layouts/layout-without-navbar-light.png" data-app-dark-img="template-layouts/layout-without-navbar-dark.png">
  </div>
  <div class="layout-demo-info">
    <h4>Layout without Navbar</h4>
    <p>Layout does not contain Navbar component.</p>
  </div>
</div>
<!--/ Layout Demo -->

@endsection
