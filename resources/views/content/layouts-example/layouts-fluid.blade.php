@php
$configData = \App\Helpers\Helpers::appClasses();
$container = 'container-fluid';
$containerNav = 'container-fluid';
@endphp

@extends('template-layouts/layoutMaster')

@section('title', 'Fluid - Layouts')

@section('content')
<!-- Layout Demo -->
<div class="layout-demo-wrapper">
  <div class="layout-demo-placeholder">
    @if ($configData['layout'] === 'horizontal')
    <img src="{{asset('assets/img/template-layouts/layout-horizontal-fluid-'.$configData['style'].'.png')}}" class="img-fluid" alt="Layout fluid" data-app-light-img="template-layouts/layout-horizontal-fluid-light.png" data-app-dark-img="template-layouts/layout-horizontal-fluid-dark.png">
    @else
    <img src="{{asset('assets/img/template-layouts/layout-fluid-'.$configData['style'].'.png')}}" class="img-fluid" alt="Layout fluid" data-app-light-img="template-layouts/layout-fluid-light.png" data-app-dark-img="template-layouts/layout-fluid-dark.png">
    @endif
  </div>
  <div class="layout-demo-info">
    <h4>Layout fluid</h4>
    <p>Fluid layout sets a <code>100% width</code> at each responsive breakpoint.</p>
  </div>
</div>
<!--/ Layout Demo -->
@endsection
