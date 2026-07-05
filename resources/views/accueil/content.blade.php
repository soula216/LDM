@php
    use App\Models\VitrineBlock;

    $hero = $blocks['hero'] ?? [];
    $gallery = $blocks['gallery'] ?? [];
    $features = $blocks['features'] ?? [];
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
        <a href="{{ VitrineBlock::resolvePublicHref($btn['href'] ?? '#') }}" class="btn btn-{{ $btn['style'] ?? 'primary' }}">
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

@if(!empty($blocks['gallery']))
{{-- Gallery - Nos Travaux --}}
<section class="gallery" id="travaux">
  <div class="section-header reveal">
    <span class="section-label">{{ $gallery['section_label'] ?? '' }}</span>
    <h2 class="section-title">{{ $gallery['section_title'] ?? '' }}</h2>
    <p class="section-subtitle">{{ $gallery['section_subtitle'] ?? '' }}</p>
  </div>
  <div class="gallery-grid">
    @foreach($gallery['items'] ?? [] as $index => $item)
      <button type="button"
              class="gallery-item reveal"
              data-gallery-item
              data-gallery-index="{{ $index }}"
              data-gallery-src="{{ $item['image_url'] ?? '' }}"
              data-gallery-title="{{ $item['title'] ?? '' }}"
              data-gallery-description="{{ $item['description'] ?? '' }}"
              aria-label="Agrandir : {{ $item['title'] ?? 'Image galerie' }}">
        <img src="{{ $item['image_url'] ?? '' }}" alt="{{ $item['title'] ?? '' }}">
        <div class="gallery-overlay">
          <h3>{{ $item['title'] ?? '' }}</h3>
          <p>{{ $item['description'] ?? '' }}</p>
        </div>
      </button>
    @endforeach
  </div>
  @include('accueil.partials.gallery-lightbox')
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

@if(!empty($blocks['contact']))
{{-- Contact Section --}}
<section class="contact-section" id="contact">
  <div class="contact-grid reveal">
    <div class="contact-card">
      <div class="contact-tag">
        <i class="{{ $contact['tag_icon'] ?? 'fas fa-comments' }}"></i>
        <span>{{ $contact['tag_text'] ?? '' }}</span>
      </div>
      <h2>{{ $contact['title'] ?? '' }}</h2>
      <p>{{ $contact['description'] ?? '' }}</p>
      <div class="contact-items">
        @foreach($contact['items'] ?? [] as $item)
          <div class="contact-item">
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
    <div class="contact-form-wrapper">
      <div class="contact-form-title">{{ $contact['form_title'] ?? '' }}</div>

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

      <form class="contact-form" action="{{ route('contact.store') }}" method="post" novalidate>
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
        </div>
        <div class="contact-actions">
          <button type="submit" class="contact-submit">
            <span>{{ $contact['form_submit_label'] ?? 'Envoyer ma demande' }}</span>
            <i class="fas fa-arrow-right"></i>
          </button>
        </div>
      </form>
    </div>
  </div>
</section>
@endif
