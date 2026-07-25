@extends('layouts.accueil')

@php
    use App\Models\VitrineBlock;
    $imageUrl = VitrineBlock::serviceItemImageUrl($service);
    $title = $service['title'] ?? 'Service';
    $hasSummary = filled($service['description'] ?? null);
    $sections = VitrineBlock::serviceSections($service);
    $hasContentHtml = filled($service['content_html'] ?? null);
    $hasMainCard = $imageUrl !== '' || $hasContentHtml || ($sections->isEmpty() && $hasSummary);
@endphp

@section('title', $title . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
<main class="inner-page service-detail-page">
  <section @class(['inner-hero', 'inner-hero--has-summary' => $hasSummary])>
    <div class="inner-hero-bg" aria-hidden="true"></div>
    <div class="inner-hero-content">
      <div class="inner-hero-badge">
        <i class="fas fa-tooth" aria-hidden="true"></i>
        <span>{{ $services['section_label'] ?? 'Nos Services' }}</span>
      </div>
      <h1>{{ $title }}</h1>
      @if($hasSummary)
        <p>{{ $service['description'] }}</p>
      @endif
      <a href="{{ route('vitrine.services') }}" class="service-detail-back">
        <i class="fas fa-arrow-left" aria-hidden="true"></i>
        <span>Retour aux services</span>
      </a>
    </div>
  </section>

  <section class="service-detail-body">
    @if($hasMainCard)
      <div class="service-detail-container">
        @if($imageUrl !== '')
          <figure class="service-detail-figure">
            <img src="{{ $imageUrl }}" alt="{{ $title }}">
          </figure>
        @endif

        @if($hasContentHtml)
          <article class="service-detail-content prose-vitrine">
            {!! $service['content_html'] !!}
          </article>
        @elseif($sections->isEmpty() && $hasSummary)
          <article class="service-detail-content prose-vitrine">
            <p>{{ $service['description'] }}</p>
          </article>
        @endif
      </div>
    @endif

    @if($sections->isNotEmpty())
      <div class="service-showcase" id="serviceSectionsGallery" data-gallery-scope>
        <div class="service-showcase__intro reveal">
          <span class="service-showcase__kicker">En détail</span>
          <h2 class="service-showcase__heading">Découvrez ce service</h2>
          <p class="service-showcase__lead">Explorez chaque aspect de notre prestation, illustré par nos réalisations.</p>
        </div>

        <div class="service-showcase__track">
          @foreach($sections as $index => $section)
            @php
              $sectionPhotos = VitrineBlock::serviceSectionPhotos($section);
              $sectionDescription = trim((string) ($section['description'] ?? ''));
              $isRichHtml = (bool) preg_match('/<\s*(?:p|h[1-6]|ul|ol|li|table|div|br|strong|em|a|img|blockquote|figure)\b/i', $sectionDescription);
              if ($isRichHtml) {
                  $sectionHtml = $sectionDescription;
              } elseif ($sectionDescription !== '') {
                  $sectionParagraphs = preg_split('/\R{2,}/', $sectionDescription) ?: [];
                  $sectionParagraphs = array_values(array_filter(array_map('trim', $sectionParagraphs)));
                  $sectionHtml = collect($sectionParagraphs)
                      ->map(fn (string $paragraph): string => '<p>'.e($paragraph).'</p>')
                      ->implode('');
              } else {
                  $sectionHtml = '';
              }
              $plainText = trim(preg_replace('/\s+/u', ' ', strip_tags($sectionDescription)) ?? '');
              $blockCount = preg_match_all('/<\s*(?:p|h[1-6]|ul|ol|table|blockquote)\b/i', $sectionHtml) ?: 0;
              $photoCount = $sectionPhotos->count();
              $galleryGroup = 'service-section-' . $index;
              $sectionTitle = trim((string) ($section['title'] ?? ''));
              $isLongDescription = mb_strlen($plainText) > 260 || $blockCount > 2;
              $mosaicClass = match (true) {
                $photoCount <= 1 => 'service-showcase__mosaic--1',
                $photoCount === 2 => 'service-showcase__mosaic--2',
                default => 'service-showcase__mosaic--bento',
              };
            @endphp
            <article @class([
              'service-showcase__block',
              'reveal',
              'service-showcase__block--alt' => $index % 2 === 1,
              'service-showcase__block--has-photos' => $photoCount > 0,
            ]) style="--showcase-delay: {{ number_format(min($index * 0.08, 0.48), 2, '.', '') }}s">
              <div class="service-showcase__block-inner">
                <div class="service-showcase__copy">
                  <div class="service-showcase__index" aria-hidden="true">
                    <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                  </div>

                  @if($sectionTitle !== '')
                    <h3 class="service-showcase__title">{{ $sectionTitle }}</h3>
                  @endif

                  @if($sectionHtml !== '')
                    <div @class([
                      'service-showcase__text',
                      'prose-vitrine',
                      'service-showcase__text--clamp' => $isLongDescription,
                    ]) data-service-section-text>
                      {!! $sectionHtml !!}
                    </div>

                    @if($isLongDescription)
                      <button type="button"
                              class="service-showcase__read-more"
                              data-service-section-read-more
                              data-section-index="{{ $index + 1 }}"
                              data-section-title="{{ $sectionTitle !== '' ? $sectionTitle : 'Section ' . ($index + 1) }}"
                              aria-haspopup="dialog">
                        <span>Lire la suite</span>
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                      </button>
                      <div class="service-showcase__full-text prose-vitrine" hidden data-service-section-full-text>
                        {!! $sectionHtml !!}
                      </div>
                    @endif
                  @endif
                </div>

                @if($photoCount > 0)
                  <div @class(['service-showcase__mosaic', $mosaicClass])>
                    @foreach($sectionPhotos as $photoIndex => $photo)
                      @php
                        $photoTitle = $photo['title'] !== ''
                          ? $photo['title']
                          : ($section['title'] ?? $title);
                      @endphp
                      <button type="button"
                              class="service-showcase__tile"
                              data-gallery-item
                              data-gallery-group="{{ $galleryGroup }}"
                              data-gallery-index="{{ $photoIndex }}"
                              data-gallery-src="{{ $photo['image_url'] }}"
                              data-gallery-title="{{ $photoTitle }}"
                              data-gallery-description="{{ $section['title'] ?? '' }}"
                              aria-label="Agrandir : {{ $photoTitle }}">
                        <span class="service-showcase__tile-visual">
                          <img src="{{ $photo['image_url'] }}"
                               alt="{{ $photoTitle }}"
                               loading="lazy"
                               decoding="async">
                          <span class="service-showcase__tile-overlay" aria-hidden="true">
                            <span class="service-showcase__tile-expand">
                              <i class="fas fa-expand"></i>
                            </span>
                          </span>
                        </span>
                        @if($photo['title'] !== '')
                          <span class="service-showcase__tile-caption">{{ $photo['title'] }}</span>
                        @endif
                      </button>
                    @endforeach
                  </div>
                @endif
              </div>
              <div class="service-showcase__glow" aria-hidden="true"></div>
            </article>
          @endforeach
        </div>
      </div>

      @include('accueil.partials.gallery-lightbox', ['modern' => true])

      <div id="serviceSectionTextModal"
           class="service-section-modal"
           hidden
           aria-hidden="true"
           role="dialog"
           aria-modal="true"
           aria-labelledby="serviceSectionTextModalTitle">
        <div class="service-section-modal__backdrop" data-service-section-modal-close></div>
        <div class="service-section-modal__shell">
          <header class="service-section-modal__header">
            <div class="service-section-modal__meta">
              <span class="service-section-modal__badge">Section</span>
              <span id="serviceSectionTextModalIndex" class="service-section-modal__index"></span>
            </div>
            <button type="button"
                    class="service-section-modal__close"
                    data-service-section-modal-close
                    aria-label="Fermer">
              <i class="fas fa-times" aria-hidden="true"></i>
            </button>
          </header>

          <div class="service-section-modal__body">
            <h2 id="serviceSectionTextModalTitle" class="service-section-modal__title"></h2>
            <div id="serviceSectionTextModalContent" class="service-section-modal__content prose-vitrine"></div>
          </div>
        </div>
      </div>
    @endif
  </section>
</main>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection

@push('scripts')
<script>
  (function initServiceSectionTextModal() {
    const modal = document.getElementById('serviceSectionTextModal');
    if (!modal) return;

    if (modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }

    const titleEl = document.getElementById('serviceSectionTextModalTitle');
    const indexEl = document.getElementById('serviceSectionTextModalIndex');
    const contentEl = document.getElementById('serviceSectionTextModalContent');
    const closeEls = modal.querySelectorAll('[data-service-section-modal-close]');
    let lastFocused = null;

    function lockBody(lock) {
      document.body.classList.toggle('service-section-modal-open', lock);
    }

    function openModal(button) {
      lastFocused = document.activeElement;

      const title = button.dataset.sectionTitle || 'Section';
      const index = button.dataset.sectionIndex || '';
      const fullText = button.parentElement?.querySelector('[data-service-section-full-text]');

      if (titleEl) titleEl.textContent = title;
      if (indexEl) {
        indexEl.textContent = index !== '' ? String(index).padStart(2, '0') : '';
        indexEl.hidden = index === '';
      }

      if (contentEl) {
        contentEl.innerHTML = fullText ? fullText.innerHTML : '';
      }

      modal.hidden = false;
      modal.setAttribute('aria-hidden', 'false');
      lockBody(true);
      requestAnimationFrame(() => modal.classList.add('is-active'));
      modal.querySelector('.service-section-modal__close')?.focus();
    }

    function closeModal() {
      modal.classList.remove('is-active');
      window.setTimeout(() => {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        lockBody(false);
        if (contentEl) contentEl.innerHTML = '';
        if (lastFocused && typeof lastFocused.focus === 'function') {
          lastFocused.focus();
        }
      }, 220);
    }

    document.addEventListener('click', (event) => {
      const trigger = event.target.closest('[data-service-section-read-more]');
      if (!trigger) return;
      event.preventDefault();
      openModal(trigger);
    });

    closeEls.forEach((el) => el.addEventListener('click', closeModal));

    document.addEventListener('keydown', (event) => {
      if (modal.hidden || !modal.classList.contains('is-active')) return;
      if (event.key === 'Escape') closeModal();
    });
  })();
</script>
@endpush
