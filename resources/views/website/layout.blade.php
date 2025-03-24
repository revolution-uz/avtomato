<!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Avtomato')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,100..900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;900&display=swap" rel="stylesheet">

        <!-- JQuery CDN -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

        <!-- Tailwind CDN -->
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

        <!-- Resources -->
        <script src="{{ asset('js/website.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('website/css/app.css') }}">
    </head>
    <body>
    <!-- Global Wrapper Start -->
    <div class="min-h-screen flex flex-col overflow-hidden">
        <!-- Get Demo Modal Start -->
        @include('website.components.getdemo_modal')
        <!-- Get Demo Modal End -->

        <!-- Message Sent Modal Start -->
        @include('website.components.message-sent_modal')
        <!-- Message Sent Modal End -->

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
    </div>
    <!-- Global Wrapper End -->
    </body>
</html>
