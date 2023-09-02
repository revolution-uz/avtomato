@php use App\Helpers\Helpers; @endphp
@isset($pageConfigs)
    {!! Helpers::updatePageConfig($pageConfigs) !!}
@endisset
@php
    $configData = Helpers::appClasses();
@endphp

@isset($configData["layout"])
    @include((( $configData["layout"] === 'horizontal') ? 'layouts.horizontalLayout' :
    (( $configData["layout"] === 'blank') ? 'layouts.blankLayout' : 'layouts.contentNavbarLayout') ))
@endisset
