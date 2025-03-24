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

        <style type="text/tailwindcss">
            @theme {
                --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
                'Segoe UI Symbol', 'Noto Color Emoji';
                --color-primary: #E21010;
                --color-secondary: #1A202C;
                --color-third: #F5F7F9;
            }

            @layer utilities {
                .gradient-circle {
                    @apply absolute rounded-full z-50;
                    width: 530px;
                    height: 530px;
                }

                .small {
                    width: 400px;
                    height: 400px;
                }

                .red-gradient {
                    background: radial-gradient(50% 50% at 50% 50%, rgba(226, 16, 16, 0.08) 0%, rgba(226, 16, 16, 0) 92%);
                }

                .yellow-gradient {
                    background: radial-gradient(50% 50% at 50% 50%, rgba(255, 213, 63, 0.08) 0%, rgba(255, 244, 205, 0) 92%);
                }

                .purple-gradient {
                    background: radial-gradient(50% 50% at 50% 50%, rgba(180, 161, 255, 0.12) 22.78%, rgba(180, 161, 255, 0) 92%);
                }
            }
        </style>

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
