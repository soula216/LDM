<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'LDM - Digital Max | Laboratoire de Prothèse Dentaire')</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @include('accueil.partials.styles')
  </style>
  @stack('styles')
</head>
<body>
  <!-- Animated Background -->
  <div class="bg-animation">
    <div class="floating-shapes">
      <div class="shape"></div>
      <div class="shape"></div>
      <div class="shape"></div>
    </div>
  </div>

  @yield('header')

  @yield('content')

  @yield('footer')

  @include('accueil.scripts')
  @stack('scripts')
</body>
</html>
