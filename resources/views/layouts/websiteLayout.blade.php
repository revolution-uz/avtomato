<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    <meta name="title" Content="Avtomato - Home">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="shortcut icon" href="{{ asset('assets/images/general/favicon.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/general/logo.png')}}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Avtomato - Home">

    <meta itemprop="name" content="Avtomato - Home">
    <meta itemprop="description" content="">
    <meta itemprop="image" content="{{ asset('assets/images/seo/64429bcdb16aa1682086861.jpg')}}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="Minstack Limited">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ asset('assets/images/seo/64429bcdb16aa1682086861.jpg')}}">
    <meta property="og:image:type" content="image/jpg">
    <meta property="og:image:width" content="1180">
    <meta property="og:image:height" content="600">
    <meta property="og:url" content="https://avtomato.uz">

    <meta name="twitter:card" content="summary_large_image">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/common/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/common/css/all.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/common/css/line-awesome.min.css')}}">

    <link rel="stylesheet" href="{{ asset('assets/presets/themesTwo/css/magnific-popup.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/presets/themesTwo/css/custom-animation.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/presets/themesTwo/css/slick.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/presets/themesTwo/css/odometer.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/presets/themesTwo/css/main.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/presets/themesTwo/css/colorb974.css?color=fab700&amp;secondColor=fab700')}}">
</head>
<body class="antialiased">
<!--==================== Preloader Start ====================-->
<div id="loading">
    <div id="loading-center">
        <div id="loading-center-absolute">
            <div class="object" id="object_one"></div>
            <div class="object" id="object_two"></div>
            <div class="object" id="object_three"></div>
        </div>
    </div>
</div>
<!--==================== Preloader End ====================-->

<!--==================== Overlay Start ====================-->
<div class="body-overlay"></div>
<!--==================== Overlay End ====================-->

<!--==================== Sidebar Overlay End ====================-->
<div class="sidebar-overlay"></div>
<!--==================== Sidebar Overlay End ====================-->

<!-- ==================== Scroll to Top End Here ==================== -->
<a class="scroll-top"><i class="fas fa-angle-double-up"></i></a>
<!-- ==================== Scroll to Top End Here ==================== -->
<!--========================== Header section Start ==========================-->
<div class="header-main-area">

    <div class="header-top">
        <div class="container-fluid">
            <div class="top-header-wrapper">
                <div class="top-contact">
                    <ul class="contact-list">
                        <li class="contact-list__item"> <span class="contact-list__item-icon"><i class="fas fa-phone"></i></span><a href="tel:+998933993300" class="contact-list__link">+998(93)-399-33-00</a></li>
                        <li class="contact-list__item"> <span class="contact-list__item-icon"><i class="fas fa-envelope"></i></span><a href="mailto:info@revolution.uz" class="contact-list__link"><span class="__cf_email__" data-cfemail="fd9c99909493bd89988e89d39e9290">info@revolution.uz</span></a></li>
                    </ul>
                </div>
                <div class="top-button">
                    <div class="language-box">
                        <select class="langSel select" onchange="window.location = this.value">
                            @foreach (config('locales.supported') as $locale)
                                <option value="{{ current_route($locale) }}" {{ app()->getLocale() === $locale ? 'selected' : '' }}>
                                    {{ \App\Helpers\LanguageHelper::getLanguageName($locale) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <ul class="login-registration-list d-flex flex-wrap justify-content-between align-items-center ms-2">
                        <li class="login-registration-list__item">
                            <ul class="social-list">
                                <li class="social-list__item"><a href="https://www.instagram.com/avtomatouz/" class="social-list__link"><i class="fab fa-instagram"></i></a> </li>
                                <li class="social-list__item"><a href="https://t.me/uzavtomato/" class="social-list__link"><i class="fab fa-telegram"></i></a> </li>
                                <li class="social-list__item"><a href="https://www.facebook.com/avtomatouz/" class="social-list__link"><i class="fab fa-facebook-f"></i></a> </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="header" id="header">
        <div class="container-fluid position-relative">
            <div class="row">
                <div class="header-wrapper">

                    <div class="logo-wrapper">

                        <a href="/" class="normal-logo" id="normal-logo"> <img src="assets/images/general/logo.png" alt="Laravel"></a>

                        <a href="/" class="dark-logo hidden" id="dark-logo"> <img src="assets/images/general/logo_white.png" alt="Laravel"></a>
                    </div>


                    <div class="menu-wrapper">
                        <ul class="main-menu">
                            <li><a href="#" aria-current="page" class="">{{__('website.home')}}</a></li>
                            <li><a href="#plan" aria-current="page" class="">{{__('website.plan')}}</a></li>
                            <li><a href="#services" aria-current="page" class="">{{__('website.services')}}</a></li>
                            <li><a href="#blog" aria-current="page" class="">{{__('website.blog')}}</a></li>
                            <li><a href="#about" aria-current="page" class="">{{__('website.about')}}</a></li>
                            <!-- <li><a href="contact.html" aria-current="page" class="">{{__('website.contact')}}</a></li> -->
                        </ul>
                    </div>

                    <div class="menu-right-wrapper">
                        <ul>
                            <li>
                            </li>
                            <li>
                                <div class="light-dark-btn-wrap ms-1" id="light-dark-checkbox">
                                    <i class="fas fa-moon mon-icon"></i>
                                    <i class='fas fa-sun sun-icon'></i>
                                </div>
                            </li>
                            <li><a class='btn btn--base' href="user/login.html">{{__("website.button_contact_us")}}</a></li>
                            <li><i class="fas fa-bars sidebar-menu-show-hide"></i></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!--========================== Header section End ==========================-->


<!--========================== Sidebar mobile menu wrap Start ==========================-->
<div class="sidebar-menu-wrapper">
    <div class="top-close d-flex align-items-center justify-content-between">
        <div class="header-wrapper">
            <div class="logo-wrapper mb-1">
                <div class="logo-wrapper">
                    <a href="https://preview.wstacks.com/finbiz" class="normal-logo" id="normal-logo"> <img src="assets/images/general/logo.png" alt="Laravel"></a>
                    <a href="https://preview.wstacks.com/finbiz" class="dark-logo hidden" id="dark-logo"> <img src="assets/images/general/logo_white.png" alt="Laravel"></a>
                </div>
            </div>
        </div>
        <i class="fas fa-times close-hide-show"></i>
    </div>
    <ul class="sidebar-menu-list">


        <li class="sidebar-menu-list__item ">
            <a href="#" class="sidebar-menu-list__link">{{__("website.home")}}</a>
        </li>
        <li class="sidebar-menu-list__item ">
            <a href="#plan" class="sidebar-menu-list__link">{{__("website.plan")}}</a>
        </li>
        <li class="sidebar-menu-list__item ">
            <a href="#services" class="sidebar-menu-list__link">{{__("website.services")}}</a>
        </li>
        <li class="sidebar-menu-list__item ">
            <a href="#blog" class="sidebar-menu-list__link">{{__("website.blog")}}</a>
        </li>
        <li class="sidebar-menu-list__item ">
            <a href="#about" class="sidebar-menu-list__link">{{__("website.about")}}</a>
        </li>
        <!-- <li class="sidebar-menu-list__item ">
            <a href="contact.html" class="sidebar-menu-list__link"> Contact</a>
        </li> -->
    </ul>
</div>
<!--========================== Sidebar mobile menu wrap End ==========================-->



<main>
    @yield('content')
</main>


<!-- ==================== Footer Start Here ==================== -->
<footer class="footer-area section-bg-light bg-img" style="background-image: url({{asset('assets/presets/themesTwo/images/footer-bg.jpg')}})">
    <span class="banner-effect-1"></span>
    <div class="pb-60 pt-80">
        <div class="container">
            <div class="row justify-content-center gy-5">
                <div class="col-xl-4 col-sm-6">
                    <div class="footer-item">
                        <div class="footer-item__logo">
                            <a href="https://preview.wstacks.com/finbiz" class="footer-logo-normal" id="footer-logo-normal"> <img src="{{asset('assets/images/general/logo_white.png')}}" alt="Laravel"></a>
                            <a href="https://preview.wstacks.com/finbiz" class="footer-logo-dark hidden" id="footer-logo-dark"> <img src="{{asset('assets/images/general/logo_white.png')}}" alt="Laravel"></a>
                        </div>
                        <p class="footer-item__desc mb-3">Let&#039;s make something great together. We are trusted by over 5000+ clients. Join them by using our services and grow your business.</p>

                        <ul class="footer-contact-menu">
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <p>15205 North Kierland Blvd</p>
                                </div>
                            </li>
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <p><a href="mailto:info@revolution.uz" data-cfemail="442520292d2a04302137306a272b29" style="color: #fff;">info@revolution.uz</a></p>
                                </div>
                            </li>
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <p><a href="tel:+998933993300" style="color: #fff;">+998(93)-399-33-00</a></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">Company Links</h5>
                        <ul class="footer-menu">
                            <li class="footer-menu__item"><a href="index.html" class="footer-menu__link">{{ __('website.home') }}</a></li>
                            <li class="footer-menu__item"><a href="about.html" class="footer-menu__link">{{__("website.about")}}</a></li>
                            <li class="footer-menu__item"><a href="cookie-policy.html" class="footer-menu__link">Cookie Policy</a></li>
                            <li class="footer-menu__item"><a href="policy/terms-of-service/43.html" class="footer-menu__link">Terms of Service</a></li>
                            <li class="footer-menu__item"><a href="policy/privacy-policy/42.html" class="footer-menu__link">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">Important Link</h5>
                        <ul class="footer-menu">
                            <li class="footer-menu__item"><a href="services.html" class="footer-menu__link">{{__("website.services")}}</a></li>
                            <!-- <li class="footer-menu__item"><a href="contact.html" class="footer-menu__link">Contact</a></li> -->
                            <li class="footer-menu__item"><a href="plan.html" class="footer-menu__link">{{__("website.plan")}}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">Newsletter</h5>

                        <p class="footer-item__desc mb-3">
                            Data transfer capacity has generally been inconsis...
                        </p>

                        <form action="https://preview.wstacks.com/finbiz/subscribe" method="POST">
                            <input type="hidden" name="_token" value="U45tYpSYzVDKgY814ggZqcYEvR3D2djVLV3DruQE">                            <div class="search-box footer w-100">
                                <input type="text" class="form--control" ame="email" placeholder="Email...">
                                <button type="submit" class="btn btn--base btn--sm">Subscribe</button>
                            </div>
                        </form>

                        <ul class="social-list">
                            <li class="social-list__item"><a href="https://www.instagram.com/avtomatouz" class="social-list__link" target="_blank"><i class="fab fa-instagram"></i></a> </li>
                            <li class="social-list__item"><a href="https://t.me/uzavtomato/" class="social-list__link" target="_blank"><i class="fab fa-telegram"></i></a> </li>
                            <li class="social-list__item"><a href="https://www.facebook.com/avtomatouz/" class="social-list__link" target="_blank"><i class="fab fa-facebook-f"></i></a> </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Top End-->

    <!-- bottom Footer -->
    <div class="bottom-footer section-bg py-3">
        <div class="container">
            <div class="row gy-2">
                <div class="col-lg-6 col-md-12">
                    <div class="bottom-footer-text"> <p>Copyright 2023. All rights reserved.</p> </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="bottom-footer-menu">
                        <ul>
                            <li><a href="policy/terms-of-service/43.html" target="_blank">Terms of Service</a></li>
                            <li><a href="policy/privacy-policy/42.html" target="_blank">Privacy Policy</a></li>
                            <li><a href="cookie-policy.html" target="_blank">Cookie Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- ==================== Footer End Here ==================== -->

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{asset('assets/common/js/jquery-3.7.0.min.js')}}"></script>
<script src="{{asset('assets/common/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/presets/themesTwo/js/popper.min.js')}}"></script>
<script src="{{asset('assets/presets/themesTwo/js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/presets/themesTwo/js/slick.min.js')}}"></script>
<script src="{{asset('assets/presets/themesTwo/js/moment.min.js')}}"></script>
<script src="{{asset('assets/presets/themesTwo/js/jquery.appear.min.js')}}"></script>
<script src="{{asset('assets/presets/themesTwo/js/jquery.magnific-popup.min.js')}}"></script>
<script src="{{asset('assets/presets/themesTwo/js/odometer.min.js')}}"></script>
<script src="{{asset('assets/presets/themesTwo/js/viewport.jquery.js')}}"></script>
<script src="{{asset('assets/presets/themesTwo/js/main.js')}}"></script>
</body>
</html>
