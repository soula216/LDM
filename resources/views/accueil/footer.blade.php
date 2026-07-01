@php
    use App\Models\VitrineBlock;

    $footer = $blocks['footer'] ?? [];
    $socialLinks = $footer['social_links'] ?? [];
    $columns = $footer['columns'] ?? [];
    $copyright = $footer['copyright'] ?? 'LDM. Tous droits réservés.';
    $legalLink = $footer['legal_link'] ?? ['label' => 'Mentions légales', 'href' => '#'];
    $logoAlt = $footer['logo_alt'] ?? 'LDM - Dentaire Moderne';
    $logoSrc = VitrineBlock::resolveLogoDisplayUrl($footer['logo_url'] ?? null);
@endphp
{{-- Footer --}}
<footer>
  <div class="footer-content">
    <div class="footer-brand">
      <a href="#accueil" class="logo">
        <img src="{{ $logoSrc }}" alt="{{ $logoAlt }}">
      </a>
      <p>{{ $footer['brand_description'] ?? '' }}</p>
      <div class="social-links">
        @foreach($socialLinks as $social)
          <a href="{{ $social['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" aria-label="Réseau social">
            <i class="{{ $social['icon'] ?? '' }}"></i>
          </a>
        @endforeach
      </div>
    </div>
    @foreach($columns as $column)
      <div class="footer-column">
        <h4>{{ $column['title'] ?? '' }}</h4>
        <ul>
          @foreach($column['links'] ?? [] as $link)
            <li>
              <a href="{{ $link['href'] ?? '#' }}" @if(!empty($link['icon'])) class="footer-link-with-icon" @endif>
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
  <div class="footer-bottom">
    <p>© {{ date('Y') }} {{ $copyright }} | <a href="{{ $legalLink['href'] ?? '#' }}" style="color: var(--primary);">{{ $legalLink['label'] ?? 'Mentions légales' }}</a></p>
  </div>
</footer>
