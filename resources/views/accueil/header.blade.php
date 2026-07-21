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
    $aboutOverviewTabs = VitrineBlock::aboutOverviewTabs();
    $aboutOverviewTabKeys = array_keys($aboutOverviewTabs);
    $aboutWorkTabs = VitrineBlock::aboutWorkTabs();
    $aboutWorkTabKeys = array_keys($aboutWorkTabs);
    $aboutCollaborationTabs = VitrineBlock::aboutCollaborationTabs();
    $aboutCollaborationTabKeys = array_keys($aboutCollaborationTabs);
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
              ($isAboutNav && (
                  VitrineBlock::isPublicPageActive('about')
                  || request()->routeIs('vitrine.services.show')
              ))
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
              @php
                $aboutOverviewRendered = false;
                $aboutWorkRendered = false;
                $aboutCollaborationRendered = false;
              @endphp
              @foreach($aboutSubPages as $key => $subPage)
                @if(in_array($key, $aboutOverviewTabKeys, true))
                  @if(! $aboutOverviewRendered)
                    @php
                      $aboutOverviewRendered = true;
                      $isOverviewActive = request()->routeIs('vitrine.about.show')
                          && (
                              request()->route('page') === VitrineBlock::aboutOverviewPageSlug()
                              || in_array(request()->route('page'), $aboutOverviewTabKeys, true)
                          );
                    @endphp
                    <li class="nav-dropdown__item nav-dropdown__item--nested @if($isOverviewActive) is-active @endif"
                        data-nav-submenu
                        role="none">
                      <button type="button"
                              class="nav-dropdown__submenu-toggle"
                              data-nav-submenu-toggle
                              role="menuitem"
                              aria-expanded="false"
                              aria-haspopup="true">
                        <span>{{ VitrineBlock::aboutOverviewPageLabel() }}</span>
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                      </button>
                      <ul class="nav-dropdown nav-dropdown--level-2" role="menu">
                        @foreach($aboutOverviewTabs as $tabKey => $tab)
                          <li role="none">
                            <a href="{{ route('vitrine.about.show', ['page' => VitrineBlock::aboutOverviewPageSlug(), 'tab' => $tabKey]) }}"
                               role="menuitem"
                               @class([
                                   'is-active' => $isOverviewActive
                                       && (request()->query('tab', 'qui-sommes-nous') === $tabKey),
                               ])>
                              {{ $tab['label'] }}
                            </a>
                          </li>
                        @endforeach
                      </ul>
                    </li>
                  @endif
                @elseif(in_array($key, $aboutWorkTabKeys, true))
                  @if(! $aboutWorkRendered)
                    @php
                      $aboutWorkRendered = true;
                      $isWorkActive = request()->routeIs('vitrine.about.show')
                          && (
                              request()->route('page') === VitrineBlock::aboutWorkPageSlug()
                              || in_array(request()->route('page'), $aboutWorkTabKeys, true)
                          );
                    @endphp
                    <li class="nav-dropdown__item nav-dropdown__item--nested @if($isWorkActive) is-active @endif"
                        data-nav-submenu
                        role="none">
                      <button type="button"
                              class="nav-dropdown__submenu-toggle"
                              data-nav-submenu-toggle
                              role="menuitem"
                              aria-expanded="false"
                              aria-haspopup="true">
                        <span>{{ VitrineBlock::aboutWorkPageLabel() }}</span>
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                      </button>
                      <ul class="nav-dropdown nav-dropdown--level-2" role="menu">
                        @foreach($aboutWorkTabs as $tabKey => $tab)
                          <li role="none">
                            <a href="{{ route('vitrine.about.show', ['page' => VitrineBlock::aboutWorkPageSlug(), 'tab' => $tabKey]) }}"
                               role="menuitem"
                               @class([
                                   'is-active' => $isWorkActive
                                       && (request()->query('tab', 'processus-de-qualite') === $tabKey),
                               ])>
                              {{ $tab['label'] }}
                            </a>
                          </li>
                        @endforeach
                      </ul>
                    </li>
                  @endif
                @elseif(in_array($key, $aboutCollaborationTabKeys, true))
                  @if(! $aboutCollaborationRendered)
                    @php
                      $aboutCollaborationRendered = true;
                      $isCollaborationActive = request()->routeIs('vitrine.about.show')
                          && (
                              request()->route('page') === VitrineBlock::aboutCollaborationPageSlug()
                              || in_array(request()->route('page'), $aboutCollaborationTabKeys, true)
                          );
                    @endphp
                    <li class="nav-dropdown__item nav-dropdown__item--nested @if($isCollaborationActive) is-active @endif"
                        data-nav-submenu
                        role="none">
                      <button type="button"
                              class="nav-dropdown__submenu-toggle"
                              data-nav-submenu-toggle
                              role="menuitem"
                              aria-expanded="false"
                              aria-haspopup="true">
                        <span>{{ VitrineBlock::aboutCollaborationPageLabel() }}</span>
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                      </button>
                      <ul class="nav-dropdown nav-dropdown--level-2" role="menu">
                        @foreach($aboutCollaborationTabs as $tabKey => $tab)
                          <li role="none">
                            <a href="{{ route('vitrine.about.show', ['page' => VitrineBlock::aboutCollaborationPageSlug(), 'tab' => $tabKey]) }}"
                               role="menuitem"
                               @class([
                                   'is-active' => $isCollaborationActive
                                       && (request()->query('tab', 'services') === $tabKey),
                               ])>
                              {{ $tab['label'] }}
                            </a>
                          </li>
                        @endforeach
                      </ul>
                    </li>
                  @endif
                @else
                  <li role="none">
                    <a href="{{ route($subPage['route'], ['page' => $key]) }}"
                       role="menuitem"
                       @class(['is-active' => request()->routeIs('vitrine.about.show') && request()->route('page') === $key])>
                      {{ $subPage['label'] }}
                    </a>
                  </li>
                @endif
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
