@php
    use App\Models\VitrineBlock;

    $hero = $blocks['hero'] ?? [];
    $gallery = $blocks['gallery'] ?? [];
    $galleryFavorites = VitrineBlock::homepageGalleryItems($gallery);
    $galleryMore = VitrineBlock::homepageGalleryMoreItems($gallery);
    $hasGallery = $galleryFavorites->isNotEmpty() || $galleryMore->isNotEmpty();
    $features = $blocks['features'] ?? [];
    $partners = $blocks['partners'] ?? [];
    $partnerItems = VitrineBlock::activePartnerItems($partners['items'] ?? []);
    $contact = $blocks['contact'] ?? [];
@endphp
{{-- Contenu principal : Hero, Gallery, Features, Contact --}}

@if(!empty($blocks['hero']))
{{-- Hero Section --}}
<section class="hero" id="accueil">
  <div class="hero-slider">
    @foreach($hero['slides'] ?? [] as $index => $slide)
      <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ $slide['image_url'] ?? '' }}');"></div>
    @endforeach
    <div class="slider-dots">
      @foreach($hero['slides'] ?? [] as $index => $slide)
        <div class="slider-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></div>
      @endforeach
    </div>
  </div>
  <div class="hero-content">
    <div class="hero-badge">
      <i class="{{ $hero['badge_icon'] ?? 'fas fa-certificate' }}"></i>
      <span>{{ $hero['badge_text'] ?? '' }}</span>
    </div>
    <h1>{{ $hero['title_line1'] ?? '' }}<br><span>{{ $hero['title_highlight'] ?? '' }}</span></h1>
    <p>{{ $hero['description'] ?? '' }}</p>
    <div class="hero-buttons">
      @foreach($hero['buttons'] ?? [] as $btn)
        @php
          $btnHref = VitrineBlock::resolvePublicHref($btn['href'] ?? '#');
          $isWhatsAppLink = VitrineBlock::isWhatsAppHref($btn['href'] ?? '') || VitrineBlock::isWhatsAppHref($btnHref);
        @endphp
        <a href="{{ $btnHref }}" class="btn btn-{{ $btn['style'] ?? 'primary' }}"@if($isWhatsAppLink) target="_blank" rel="noopener noreferrer"@endif>
          @if(!empty($btn['icon']))
            <i class="{{ $btn['icon'] }}"></i>
          @endif
          {{ $btn['label'] ?? '' }}
        </a>
      @endforeach
    </div>
  </div>
  <div class="hero-visual">
    <div class="hero-card">
      <div class="hero-card-icon">
        <i class="{{ $hero['card']['icon'] ?? 'fas fa-tooth' }}"></i>
      </div>
      <h3>{{ $hero['card']['title'] ?? '' }}</h3>
      <p>{{ $hero['card']['description'] ?? '' }}</p>
      <div class="hero-stats">
        @foreach($hero['card']['stats'] ?? [] as $stat)
          <div class="stat">
            <div class="stat-value">{{ $stat['value'] ?? '' }}</div>
            <div class="stat-label">{{ $stat['label'] ?? '' }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

@if(!empty($blocks['gallery']) && $hasGallery)
{{-- Gallery - Nos Travaux --}}
<section class="gallery" id="travaux">
  <div class="section-header reveal">
    <span class="section-label">{{ $gallery['section_label'] ?? '' }}</span>
    <h2 class="section-title">{{ $gallery['section_title'] ?? '' }}</h2>
    <p class="section-subtitle">{{ $gallery['section_subtitle'] ?? '' }}</p>
  </div>
  <div class="gallery-grid" data-homepage-gallery>
    @foreach($galleryFavorites as $index => $item)
      <button type="button"
              class="gallery-item reveal"
              data-gallery-item
              data-gallery-index="{{ $index }}"
              data-gallery-src="{{ $item['image_url'] ?? '' }}"
              data-gallery-title="{{ $item['title'] ?? '' }}"
              data-gallery-description="{{ $item['description'] ?? '' }}"
              aria-label="Agrandir : {{ $item['title'] ?? 'Image galerie' }}">
        <img src="{{ $item['image_url'] ?? '' }}" alt="{{ $item['title'] ?? '' }}" loading="lazy">
        <div class="gallery-overlay">
          <h3>{{ $item['title'] ?? '' }}</h3>
          <p>{{ $item['description'] ?? '' }}</p>
        </div>
      </button>
    @endforeach
    @foreach($galleryMore as $index => $item)
      <button type="button"
              class="gallery-item reveal gallery-item--extra"
              data-gallery-item
              data-gallery-index="{{ $galleryFavorites->count() + $index }}"
              data-gallery-src="{{ $item['image_url'] ?? '' }}"
              data-gallery-title="{{ $item['title'] ?? '' }}"
              data-gallery-description="{{ $item['description'] ?? '' }}"
              aria-label="Agrandir : {{ $item['title'] ?? 'Image galerie' }}"
              hidden>
        <img src="{{ $item['image_url'] ?? '' }}" alt="{{ $item['title'] ?? '' }}" loading="lazy">
        <div class="gallery-overlay">
          <h3>{{ $item['title'] ?? '' }}</h3>
          <p>{{ $item['description'] ?? '' }}</p>
        </div>
      </button>
    @endforeach
  </div>
  @if($galleryMore->isNotEmpty())
    <div class="gallery-more" data-gallery-more>
      <a href="{{ route('vitrine.gallery') }}" class="btn btn-secondary gallery-more__btn">
        Voir la suite
        <i class="fas fa-arrow-right" aria-hidden="true"></i>
      </a>
    </div>
  @endif
  @include('accueil.partials.gallery-lightbox', ['modern' => true])
</section>
@endif

@if(!empty($blocks['features']))
{{-- Features Section --}}
<section class="features" id="about">
  <div class="features-container">
    <div class="features-content reveal">
      <h2>{{ $features['title_before'] ?? '' }} <span>{{ $features['title_highlight'] ?? '' }}</span>{{ $features['title_after'] ?? '' }}</h2>
      <p style="color: var(--text-muted); margin-bottom: 1rem;">{{ $features['description'] ?? '' }}</p>
      <ul class="features-list">
        @foreach($features['list'] ?? [] as $point)
          <li>
            <i class="fas fa-check-circle"></i>
            <span>{{ $point }}</span>
          </li>
        @endforeach
      </ul>
    </div>
    <div class="features-visual reveal">
      <div class="features-card">
        <div class="features-card-icon">
          <i class="{{ $features['card']['icon'] ?? 'fas fa-award' }}"></i>
        </div>
        <h3>{{ $features['card']['title'] ?? '' }}</h3>
        <p>{{ $features['card']['description'] ?? '' }}</p>
      </div>
    </div>
  </div>
</section>
@endif

@if(!empty($blocks['partners']) && $partnerItems->isNotEmpty())
{{-- Partners - Nos Partenaires --}}
<section class="partners" id="partenaires">
  <div class="partners__bg" aria-hidden="true">
    <span class="partners__mesh"></span>
    <span class="partners__orb partners__orb--1"></span>
    <span class="partners__orb partners__orb--2"></span>
    <span class="partners__orb partners__orb--3"></span>
  </div>

  <div class="partners__container">
    <div class="partners-showcase__head reveal">
      <div class="partners-showcase__badge">
        <i class="fas fa-handshake" aria-hidden="true"></i>
        <span>{{ $partners['section_label'] ?? 'Nos Partenaires' }}</span>
      </div>
      <h2 class="partners-showcase__title">{{ $partners['section_title'] ?? 'Ils Nous Font Confiance' }}</h2>
      @if(filled($partners['section_subtitle'] ?? null))
        <p class="partners-showcase__subtitle">{{ $partners['section_subtitle'] }}</p>
      @endif
      <div class="partners-showcase__meta">
        <span class="partners-showcase__pill">{{ $partnerItems->count() }} partenaire{{ $partnerItems->count() > 1 ? 's' : '' }} de référence</span>
      </div>
    </div>

    <div class="partners-showcase reveal" data-partners-carousel>
      <div class="partners-showcase__rim" aria-hidden="true"></div>
      <div class="partners-showcase__glow" aria-hidden="true"></div>

      <div class="partners-carousel">
        <div class="partners-carousel__fade partners-carousel__fade--left" aria-hidden="true"></div>
        <div class="partners-carousel__fade partners-carousel__fade--right" aria-hidden="true"></div>

        <div class="partners-carousel__viewport">
          <div class="partners-carousel__track" data-partners-track>
            @foreach([1, 2] as $loopPass)
              @foreach($partnerItems as $item)
                @php
                  $partnerUrl = filled($item['url'] ?? null) ? VitrineBlock::resolvePublicHref($item['url']) : null;
                  $partnerName = $item['name'] ?? 'Partenaire';
                @endphp
                <article class="partners-carousel__slide">
                  @if($partnerUrl)
                    <a href="{{ $partnerUrl }}" class="partners-card" target="_blank" rel="noopener noreferrer" aria-label="{{ $partnerName }}">
                  @else
                    <div class="partners-card" aria-label="{{ $partnerName }}">
                  @endif
                      <span class="partners-card__shine" aria-hidden="true"></span>
                      <div class="partners-card__inner">
                        <img src="{{ $item['image_url'] ?? '' }}"
                             alt="{{ $partnerName }}"
                             loading="lazy"
                             decoding="async"
                             class="partners-card__logo">
                      </div>
                      @if(filled($partnerName))
                        <span class="partners-card__name">{{ $partnerName }}</span>
                      @endif
                  @if($partnerUrl)
                    </a>
                  @else
                    </div>
                  @endif
                </article>
              @endforeach
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endif

@if(!empty($blocks['contact']))
@php
    $showContactInfo = filter_var($contact['info_is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $showContactForm = filter_var($contact['form_is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $showContactMap = VitrineBlock::isContactMapActive($contact);
    $contactMapEmbedUrl = VitrineBlock::contactMapEmbedUrl($contact);
    $contactMapLinkUrl = VitrineBlock::contactMapLinkUrl($contact);
    $mapBelowRow = $showContactMap && $showContactInfo && $showContactForm;
    $mapInRow = $showContactMap && ! $mapBelowRow;
    $contactRowBlocks = (int) $showContactInfo + (int) $showContactForm + (int) $mapInRow;
@endphp
@if($showContactInfo || $showContactForm || $showContactMap)
{{-- Contact Section --}}
<section class="contact-section" id="contact">
  <div class="contact-section__bg" aria-hidden="true">
    <span class="contact-section__orb contact-section__orb--1"></span>
    <span class="contact-section__orb contact-section__orb--2"></span>
    <span class="contact-section__orb contact-section__orb--3"></span>
  </div>

  <div class="contact-layout">
    <div @class([
      'contact-row reveal',
      'contact-row--single' => $contactRowBlocks === 1,
      'contact-row--duo' => $contactRowBlocks === 2,
      'contact-row--info-form' => $showContactInfo && $showContactForm && ! $mapInRow,
    ])>
    @if($showContactInfo)
    <div class="contact-card contact-glass">
      <div class="contact-tag">
        <i class="{{ $contact['tag_icon'] ?? 'fas fa-comments' }}"></i>
        <span>{{ $contact['tag_text'] ?? '' }}</span>
      </div>
      <h2>{{ $contact['title'] ?? '' }}</h2>
      <p class="contact-card__lead">{{ $contact['description'] ?? '' }}</p>
      <div class="contact-items">
        @foreach($contact['items'] ?? [] as $item)
          <div class="contact-item contact-glass-item">
            <div class="contact-item-icon"><i class="{{ $item['icon'] ?? '' }}"></i></div>
            <div class="contact-item-text">
              <h4>{{ $item['title'] ?? '' }}</h4>
              @if(filled($item['value_1'] ?? null) || filled($item['value'] ?? null))
                <p>{{ $item['value_1'] ?? $item['value'] ?? '' }}</p>
              @endif
              @if(filled($item['value_2'] ?? null))
                <p>{{ $item['value_2'] }}</p>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($showContactForm)
    <div class="contact-form-wrapper contact-glass">
      <div class="contact-form-header">
        <div class="contact-form-title">{{ $contact['form_title'] ?? '' }}</div>
        <p class="contact-form-subtitle">{{ $contact['form_subtitle'] ?? 'Réponse sous 24 h ouvrées' }}</p>
      </div>

      @if(session('contact_success'))
        <div class="contact-alert contact-alert--success" role="status">
          <i class="fas fa-check-circle" aria-hidden="true"></i>
          <span>{{ session('contact_success') }}</span>
        </div>
      @endif

      @if($errors->any())
        <div class="contact-alert contact-alert--error" role="alert">
          <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
          <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
      @endif

      <form class="contact-form" action="{{ route('contact.store') }}" method="post" enctype="multipart/form-data" novalidate data-contact-form>
        @csrf
        <div class="contact-form-grid">
          <div>
            <label class="contact-label" for="contact-name">Nom complet <span aria-hidden="true">*</span></label>
            <input id="contact-name" name="name" type="text" class="contact-input @error('name') contact-input--error @enderror" placeholder="Votre nom" value="{{ old('name') }}" required autocomplete="name">
            @error('name')
              <p class="contact-field-error">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="contact-label" for="contact-email">Email <span aria-hidden="true">*</span></label>
            <input id="contact-email" name="email" type="email" class="contact-input @error('email') contact-input--error @enderror" placeholder="Votre email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
              <p class="contact-field-error">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="contact-label" for="contact-phone">Téléphone</label>
            <input id="contact-phone" name="phone" type="tel" class="contact-input @error('phone') contact-input--error @enderror" placeholder="Votre téléphone" value="{{ old('phone') }}" autocomplete="tel">
            @error('phone')
              <p class="contact-field-error">{{ $message }}</p>
            @enderror
          </div>
          <div class="full-row">
            <label class="contact-label" for="contact-message">Message <span aria-hidden="true">*</span></label>
            <textarea id="contact-message" name="message" class="contact-textarea @error('message') contact-input--error @enderror" placeholder="Décrivez votre projet, vos besoins ou vos questions…" required>{{ old('message') }}</textarea>
            @error('message')
              <p class="contact-field-error">{{ $message }}</p>
            @enderror
          </div>
          <div class="full-row">
            <label class="contact-label" for="contact-attachment">Pièce jointe <span class="contact-label-optional">(optionnel)</span></label>
            <input id="contact-attachment" name="attachment" type="file" class="contact-file @error('attachment') contact-input--error @enderror" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx,.stl,application/pdf,image/jpeg,image/png,image/webp,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,model/stl,application/sla,application/vnd.ms-pki.stl,application/octet-stream">
            <p class="contact-file-hint">PDF, images, Word ou STL — 45 Mo maximum</p>
            @error('attachment')
              <p class="contact-field-error">{{ $message }}</p>
            @enderror
          </div>
        </div>
        <div class="contact-actions">
          <button type="submit" class="contact-submit" data-contact-submit>
            <span class="contact-submit__spinner" aria-hidden="true"></span>
            <span class="contact-submit__label">{{ $contact['form_submit_label'] ?? 'Envoyer ma demande' }}</span>
            <i class="fas fa-arrow-right contact-submit__icon"></i>
          </button>
        </div>
      </form>
    </div>
    @endif

    @if($mapInRow)
    <div class="contact-map contact-map--inline contact-glass">
      @if(filled($contact['map_title'] ?? null))
        <div class="contact-map__head">
          <h3 class="contact-map__title">{{ $contact['map_title'] }}</h3>
          @if(filled($contact['map_address'] ?? null))
            <p class="contact-map__address">{{ $contact['map_address'] }}</p>
          @endif
        </div>
      @endif
      <div class="contact-map__frame">
        <iframe
          src="{{ $contactMapEmbedUrl }}"
          title="{{ $contact['map_title'] ?? 'Localisation' }}"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen>
        </iframe>
      </div>
      @if(filled($contactMapLinkUrl))
        <div class="contact-map__actions">
          <a href="{{ $contactMapLinkUrl }}" class="contact-map__link" target="_blank" rel="noopener noreferrer">
            <i class="fas fa-location-arrow" aria-hidden="true"></i>
            <span>Ouvrir dans Google Maps</span>
          </a>
        </div>
      @endif
    </div>
    @endif
    </div>

  @if($mapBelowRow)
    <div class="contact-map contact-map--below contact-glass reveal">
      @if(filled($contact['map_title'] ?? null))
        <div class="contact-map__head">
          <h3 class="contact-map__title">{{ $contact['map_title'] }}</h3>
          @if(filled($contact['map_address'] ?? null))
            <p class="contact-map__address">{{ $contact['map_address'] }}</p>
          @endif
        </div>
      @endif
      <div class="contact-map__frame">
        <iframe
          src="{{ $contactMapEmbedUrl }}"
          title="{{ $contact['map_title'] ?? 'Localisation' }}"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen>
        </iframe>
      </div>
      @if(filled($contactMapLinkUrl))
        <div class="contact-map__actions">
          <a href="{{ $contactMapLinkUrl }}" class="contact-map__link" target="_blank" rel="noopener noreferrer">
            <i class="fas fa-location-arrow" aria-hidden="true"></i>
            <span>Ouvrir dans Google Maps</span>
          </a>
        </div>
      @endif
    </div>
  @endif
  </div>
</section>
@endif
@endif
