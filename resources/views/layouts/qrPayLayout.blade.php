<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body {
            height: 100%;
            background-color: rgb(247, 247, 247);
        }

        .container {
            width: 100%;
            max-width: 640px;
            padding: 0 20px;
            margin: 0 auto;
        }

        .inactive-button {
            background-color: #ECECF0;
            color: #979797;
            pointer-events: none;
            cursor: not-allowed;
        }

        .active-button {
            background-color: #00AB56;
            color: #FFFFFF;
        }

        .custom-text-gray {
            color: #0F12258C;
        }

        .custom-text-black {
            color: #0F1225;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="flex flex-col justify-between h-full container">
    @yield('content')
</div>
</body>
</html>
