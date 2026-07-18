@php
    use App\Models\VitrineBlock;

    $header = $blocks['header'] ?? [];
    $footer = $blocks['footer'] ?? [];
    $navLinks = collect($header['nav_links'] ?? [])
        ->filter(fn ($link) => filter_var($link['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN))
        ->values()
        ->all();
    $socialLinks = $footer['social_links'] ?? [];
    $clientLabel = $header['client_space_label'] ?? 'Espace client';
    $clientSpaceIsActive = filter_var($header['client_space_is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $headerLogoAlt = $header['logo_alt'] ?? 'LDM - Digital Max';
    $headerLogoSrc = VitrineBlock::resolveLogoDisplayUrl($header['logo_url'] ?? null);
    $footerLogoAlt = $footer['logo_alt'] ?? $headerLogoAlt;
    $footerLogoSrc = VitrineBlock::resolveLogoDisplayUrl($footer['logo_url'] ?? null);
    $homeUrl = route('vitrine');
    $aboutSubPages = VitrineBlock::orderedAboutSubPages($blocks['about'] ?? []);
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
    <a href="{{ $homeUrl }}" class="logo">
      <img src="{{ $headerLogoSrc }}" alt="{{ $headerLogoAlt }}" class="logo-img logo-img-header">
      <img src="{{ $footerLogoSrc }}" alt="{{ $footerLogoAlt }}" class="logo-img logo-img-scrolled">
    </a>
    <ul class="nav-links">
      @foreach($navLinks as $link)
        @php
          $navHref = VitrineBlock::resolvePublicHref($link['href'] ?? '#');
          $navPath = parse_url($navHref, PHP_URL_PATH) ?: $navHref;
          $isAboutNav = str_contains($navHref, '/le-laboratoire')
              || preg_match('#/(about)/?$#', $navPath) === 1;
          $isActive = (
              ($isAboutNav && VitrineBlock::isPublicPageActive('about'))
              || (VitrineBlock::isPublicPageActive('academy') && str_contains($navHref, '/academy'))
              || (VitrineBlock::isPublicPageActive('services') && str_contains($navHref, '/services'))
              || (VitrineBlock::isPublicPageActive('process') && str_contains($navHref, '/process'))
              || (VitrineBlock::isPublicPageActive('gallery') && ($navPath === '/gallery' || $navPath === 'gallery'))
              || (VitrineBlock::isPublicPageActive('faq') && str_contains($navHref, '/faq'))
              || (VitrineBlock::isPublicPageActive('vos-patients') && str_contains($navHref, '/vos-patients'))
              || (VitrineBlock::isPublicPageActive('recrutement') && str_contains($navHref, '/recrutement'))
          );
        @endphp
        @if($isAboutNav)
          <li class="nav-item nav-item--dropdown" data-nav-dropdown>
            <button type="button"
                    class="nav-dropdown-toggle @if($isActive) is-active @endif"
                    data-nav-dropdown-toggle
                    aria-expanded="false"
                    aria-haspopup="true">
              <span>{{ $link['label'] ?? 'Le Laboratoire' }}</span>
              <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </button>
            <ul class="nav-dropdown" role="menu">
              @foreach($aboutSubPages as $key => $subPage)
                <li role="none">
                  <a href="{{ route($subPage['route'], ['page' => $key]) }}"
                     role="menuitem"
                     @class(['is-active' => request()->routeIs('vitrine.about.show') && request()->route('page') === $key])>
                    {{ $subPage['label'] }}
                  </a>
                </li>
              @endforeach
            </ul>
          </li>
        @else
          <li>
            <a href="{{ $navHref }}" @class(['is-active' => $isActive])>
              {{ $link['label'] ?? '' }}
            </a>
          </li>
        @endif
      @endforeach
      @if($clientSpaceIsActive)
        <li class="nav-espace-client-desktop"><a href="{{ route('login') }}">{{ $clientLabel }}</a></li>
      @endif
    </ul>
    <div class="nav-mobile-right">
      @if($clientSpaceIsActive)
        <a href="{{ route('login') }}" class="nav-espace-client-mobile">{{ $clientLabel }}</a>
      @endif
      <button type="button" class="menu-toggle" id="menuToggle" onclick="toggleMenu()" aria-label="Ouvrir le menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </nav>
</header>
