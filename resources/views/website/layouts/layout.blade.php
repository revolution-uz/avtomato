<!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#E21010" />

        <title>@yield('title', 'Avtomato')</title>

        @yield('meta')

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
    <!-- Global Wrapper Start -->
    <div class="min-h-screen flex flex-col overflow-hidden pt-20">
        <!-- Header Start -->
        @include('website.components.header')
        <!-- Header End -->

        <!-- Burger Menu Start -->
        @include('website.components.burger_menu')
        <!-- Burger Menu End -->

        <!-- Main Content Start -->
        <main>
            @yield('content')
        </main>
        <!-- Main Content End -->

        <!-- Footer Start -->
        @include('website.components.footer')
        <!-- Footer End -->

        <!-- Get Demo Modal Start -->
        @include('website.components.getdemo_modal')
        <!-- Get Demo Modal End -->

        <!-- Message Sent Modal Start -->
        @include('website.components.message-sent_modal')
        <!-- Message Sent Modal End -->
    </div>
    <!-- Global Wrapper End -->
    </body>
</html>
