@php use App\Helpers\Helpers; @endphp
@isset($pageConfigs)
    {!! Helpers::updatePageConfig($pageConfigs) !!}
@endisset
@php
    $configData = Helpers::appClasses();
@endphp

@isset($configData["layout"])
    @include((( $configData["layout"] === 'horizontal') ? 'template-layouts.horizontalLayout' :
    (( $configData["layout"] === 'blank') ? 'template-layouts.blankLayout' : 'template-layouts.contentNavbarLayout') ))
@endisset
