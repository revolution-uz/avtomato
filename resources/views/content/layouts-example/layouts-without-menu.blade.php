@php
$configData = \App\Helpers\Helpers::appClasses();
$isMenu = false;
$navbarHideToggle = false;
@endphp

@extends('template-layouts/layoutMaster')

@section('title', 'Without menu - Layouts')

@section('content')

<!-- Layout Demo -->
<div class="layout-demo-wrapper">
  <div class="layout-demo-placeholder">
    @if ($configData['layout'] === 'horizontal')
    <img src="{{asset('assets/img/template-layouts/layout-horizontal-without-menu-'.$configData['style'].'.png')}}" class="img-fluid" alt="Layout without menu" data-app-light-img="template-layouts/layout-horizontal-without-menu-light.png" data-app-dark-img="template-layouts/layout-horizontal-without-menu-dark.png">
    @else
    <img src="{{asset('assets/img/template-layouts/layout-without-menu-'.$configData['style'].'.png')}}" class="img-fluid" alt="Layout without menu" data-app-light-img="template-layouts/layout-without-menu-light.png" data-app-dark-img="template-layouts/layout-without-menu-dark.png">
    @endif
  </div>
  <div class="layout-demo-info">
    <h4>Layout without Menu (Navigation)</h4>
    <button class="btn btn-primary" type="button" onclick="history.back()">Go Back</button>
  </div>
</div>
<!--/ Layout Demo -->

@endsection
