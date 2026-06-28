<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LDM') }} - Connexion</title>

        <!-- Fonts - Manrope selon la charte LDM v2 -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @include('partials.app-assets')

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="bg-[#0a0e1a]" style="background-color: #0a0e1a !important; overflow-x: hidden;">
        <div class="font-sans antialiased">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
