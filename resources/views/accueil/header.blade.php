@php
    use App\Models\VitrineBlock;

    $header = $blocks['header'] ?? [];
    $footer = $blocks['footer'] ?? [];
    $navLinks = $header['nav_links'] ?? [];
    $socialLinks = $footer['social_links'] ?? [];
    $clientLabel = $header['client_space_label'] ?? 'Espace client';
    $headerLogoAlt = $header['logo_alt'] ?? 'LDM - Digital Max';
    $headerLogoSrc = VitrineBlock::resolveLogoDisplayUrl($header['logo_url'] ?? null);
    $footerLogoAlt = $footer['logo_alt'] ?? $headerLogoAlt;
    $footerLogoSrc = VitrineBlock::resolveLogoDisplayUrl($footer['logo_url'] ?? null);
    $homeUrl = route('vitrine');
@endphp
{{-- Header / Navigation --}}
<header class="site-header" id="siteHeader">
  <div class="top-bar">
    <div class="top-bar-inner">
      <span class="top-bar-label">Suivez-nous</span>
      <span class="top-bar-accent" aria-hidden="true"></span>
      @include('accueil.partials.social-links', [
          'links' => $socialLinks,
          'modifier' => 'social-links--topbar',
          'flagSrc' => asset('images/vitrine/drapeau-tunis.png'),
      ])
    </div>
  </div>
  <nav id="navbar">
    <a href="{{ $homeUrl }}#accueil" class="logo">
      <img src="{{ $headerLogoSrc }}" alt="{{ $headerLogoAlt }}" class="logo-img logo-img-header">
      <img src="{{ $footerLogoSrc }}" alt="{{ $footerLogoAlt }}" class="logo-img logo-img-scrolled">
    </a>
    <ul class="nav-links">
      @foreach($navLinks as $link)
        @php
          $navHref = VitrineBlock::resolvePublicHref($link['href'] ?? '#');
          $isActive = (
              (VitrineBlock::isPublicPageActive('academy') && str_contains($navHref, '/academy'))
              || (VitrineBlock::isPublicPageActive('services') && str_contains($navHref, '/services'))
              || (VitrineBlock::isPublicPageActive('process') && str_contains($navHref, '/process'))
              || (VitrineBlock::isPublicPageActive('gallery') && str_contains($navHref, '/gallery'))
              || (VitrineBlock::isPublicPageActive('faq') && str_contains($navHref, '/faq'))
          );
        @endphp
        <li>
          <a href="{{ $navHref }}" @class(['is-active' => $isActive])>
            {{ $link['label'] ?? '' }}
          </a>
        </li>
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
</header>
