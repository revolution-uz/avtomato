@php
$configData = \App\Helpers\Helpers::appClasses();
@endphp

@extends('template-layouts/layoutMaster')

@section('title', 'Content navbar - Layouts')

@section('content')

<!-- Layout Demo -->
<div class="layout-demo-wrapper">
  <div class="layout-demo-placeholder">
    <img src="{{asset('assets/img/template-layouts/layout-content-navbar-'.$configData['style'].'.png')}}" class="img-fluid" alt="Layout content navbar" data-app-light-img="template-layouts/layout-content-navbar-light.png" data-app-dark-img="template-layouts/layout-content-navbar-dark.png">
  </div>
  <div class="layout-demo-info">
    <h4>Layout content navbar</h4>
    <p>Template default layout, navbar in content area.</p>
  </div>
</div>
<!--/ Layout Demo -->
@endsection
