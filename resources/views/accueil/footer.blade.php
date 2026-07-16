@php
    use App\Models\VitrineBlock;

    $footer = $blocks['footer'] ?? [];
    $socialLinks = $footer['social_links'] ?? [];
    $columns = $footer['columns'] ?? [];
    $copyright = $footer['copyright'] ?? 'LDM. Tous droits réservés.';
    $legalLink = $footer['legal_link'] ?? ['label' => 'Mentions légales', 'href' => '#'];
    $logoAlt = $footer['logo_alt'] ?? 'LDM - Digital Max';
    $logoSrc = VitrineBlock::resolveLogoDisplayUrl($footer['logo_url'] ?? null);
@endphp
{{-- Footer --}}
<footer>
  <div class="footer-content">
    <div class="footer-brand">
      <a href="{{ route('vitrine') }}" class="logo">
        <img src="{{ $logoSrc }}" alt="{{ $logoAlt }}">
      </a>
      <p>{{ $footer['brand_description'] ?? '' }}</p>
      @include('accueil.partials.social-links', [
          'links' => $socialLinks,
          'flagSrc' => asset('images/vitrine/drapeau-tunis.png'),
      ])
    </div>
    <div class="footer-columns">
      @foreach($columns as $column)
        <div class="footer-column">
          <h4>{{ $column['title'] ?? '' }}</h4>
          <ul>
            @foreach($column['links'] ?? [] as $link)
              <li>
                <a href="{{ VitrineBlock::resolveFooterLinkHref($link['href'] ?? '#', $link['label'] ?? '', $link['icon'] ?? null) }}" @if(!empty($link['icon'])) class="footer-link-with-icon" @endif>
                  @if(!empty($link['icon']))
                    <i class="{{ $link['icon'] }}" aria-hidden="true"></i>
                  @endif
                  <span>{{ $link['label'] ?? '' }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      @endforeach
    </div>
  </div>
  <div class="footer-bottom">
    <p>© {{ date('Y') }} {{ $copyright }} | <a href="{{ VitrineBlock::resolvePublicHref($legalLink['href'] ?? '#') }}" style="color: var(--primary);">{{ $legalLink['label'] ?? 'Mentions légales' }}</a></p>
  </div>
</footer>
