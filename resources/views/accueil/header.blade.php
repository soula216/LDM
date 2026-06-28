{{-- Header / Navigation --}}
<nav id="navbar">
  <a href="#accueil" class="logo">
    <img src="{{ asset('logo_ldm.png') }}" alt="LDM - Dentaire Moderne">
  </a>
  <ul class="nav-links">
    <li><a href="#accueil">Accueil</a></li>
    <li><a href="#services">Services</a></li>
    <li><a href="#process">Process</a></li>
    <li><a href="#contact">Contact</a></li>
    <li class="nav-espace-client-desktop"><a href="{{ route('login') }}">Espace client</a></li>
  </ul>
  <div class="nav-mobile-right">
    <a href="{{ route('login') }}" class="nav-espace-client-mobile">Espace client</a>
    <button type="button" class="menu-toggle" id="menuToggle" onclick="toggleMenu()" aria-label="Ouvrir le menu" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
</nav>
