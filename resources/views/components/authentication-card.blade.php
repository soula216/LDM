@props([
    'title' => 'Connexion Sécurisée',
    'subtitle' => 'Gestion de laboratoire prothèse dentaire',
    'wide' => false,
])

<div class="login-bg-container">
    <!-- Background Image - Fallback to gradient if image doesn't exist -->
    @if(file_exists(public_path('bg-dentaire.jpg')))
        <img src="{{ asset('bg-dentaire.jpg') }}" alt="Labo prothèse" class="login-bg-image" loading="eager">
    @else
        <!-- Fallback gradient background if image doesn't exist -->
        <div class="login-bg-image" style="background: linear-gradient(135deg, #0a0e1a 0%, #2a5aa0 40%, #00d4aa 100%); width: 100%; height: 100%; position: absolute; top: 0; left: 0;"></div>
    @endif
    
    <!-- Overlay gradient + particles -->
    <div class="login-bg-overlay"></div>
    <div class="login-particles"></div>
</div>

<main class="login-container-new">
    <div @class(['login-card-new', 'login-card-new--wide' => $wide])>
        <header class="login-card-header login-animate-float">
            <div class="login-logo-container">
                <div class="login-logo-ring">
                    <div class="login-logo-ring-inner">LDM</div>
                </div>
            </div>
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle }}</p>
        </header>

        {{ $slot }}

        <footer class="login-card-footer">
            <span>© {{ date('Y') }} Prothèse Labo - Tous droits réservés</span>
        </footer>
    </div>
</main>
