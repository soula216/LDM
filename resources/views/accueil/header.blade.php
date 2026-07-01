@php
    use App\Models\VitrineBlock;

    $header = $blocks['header'] ?? [];
    $footer = $blocks['footer'] ?? [];
    $navLinks = $header['nav_links'] ?? [];
    $clientLabel = $header['client_space_label'] ?? 'Espace client';
    $headerLogoAlt = $header['logo_alt'] ?? 'LDM - Dentaire Moderne';
    $headerLogoSrc = VitrineBlock::resolveLogoDisplayUrl($header['logo_url'] ?? null);
    $footerLogoAlt = $footer['logo_alt'] ?? $headerLogoAlt;
    $footerLogoSrc = VitrineBlock::resolveLogoDisplayUrl($footer['logo_url'] ?? null);
@endphp
{{-- Header / Navigation --}}
<nav id="navbar">
  <a href="#accueil" class="logo">
    <img src="{{ $headerLogoSrc }}" alt="{{ $headerLogoAlt }}" class="logo-img logo-img-header">
    <img src="{{ $footerLogoSrc }}" alt="{{ $footerLogoAlt }}" class="logo-img logo-img-scrolled">
  </a>
  <ul class="nav-links">
    @foreach($navLinks as $link)
      <li><a href="{{ $link['href'] ?? '#' }}">{{ $link['label'] ?? '' }}</a></li>
    @endforeach
    <li class="nav-espace-client-desktop"><a href="{{ route('login') }}">{{ $clientLabel }}</a></li>
  </ul>
  <div class="nav-mobile-right">
    <a href="{{ route('login') }}" class="nav-espace-client-mobile">{{ $clientLabel }}</a>
    <button type="button" class="menu-toggle" id="menuToggle" onclick="toggleMenu()" aria-label="Ouvrir le menu" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
</nav>
