@extends('layouts.accueil')

@section('title', ($laboratory['title'] ?? 'Laboratoire / Équipe') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;

    $photos = VitrineBlock::laboratoryPhotos($laboratory);
    $categories = VitrineBlock::laboratoryCategories();
    $counts = $photos->countBy('category');
@endphp

<main class="laboratory-page">
  <section class="laboratory-hero">
    <div class="laboratory-hero__bg" aria-hidden="true"></div>
    <div class="laboratory-hero__mesh" aria-hidden="true"></div>
    <div class="laboratory-hero__content">
      <div class="laboratory-hero__badge">
        <i class="fas fa-users" aria-hidden="true"></i>
        <span>{{ $laboratory['section_label'] ?? 'Laboratoire / Équipe' }}</span>
      </div>
      <h1>{{ $laboratory['title'] ?? 'Notre équipe & nos installations' }}</h1>
      @if(filled($laboratory['description'] ?? null))
        <p class="laboratory-hero__lead">{{ $laboratory['description'] }}</p>
      @endif
      @if($photos->isNotEmpty())
        <div class="laboratory-hero__stats">
          <div class="laboratory-hero__stat">
            <strong>{{ $photos->count() }}</strong>
            <span>photo{{ $photos->count() > 1 ? 's' : '' }}</span>
          </div>
          @foreach($categories as $key => $meta)
            @if(($counts[$key] ?? 0) > 0)
              <div class="laboratory-hero__stat">
                <strong>{{ $counts[$key] }}</strong>
                <span>{{ $meta['label'] }}</span>
              </div>
            @endif
          @endforeach
        </div>
      @endif
    </div>
  </section>

  <section class="laboratory-body">
    @if($photos->isEmpty())
      <div class="laboratory-empty reveal">
        <div class="laboratory-empty__icon"><i class="fas fa-camera" aria-hidden="true"></i></div>
        <h2>Galerie à venir</h2>
        <p>Les photos de l'équipe, du laboratoire et des équipements seront bientôt disponibles.</p>
      </div>
    @else
      <div class="laboratory-toolbar reveal">
        <div class="laboratory-filters" role="tablist" aria-label="Filtrer par catégorie">
          <button type="button"
                  class="laboratory-filter is-active"
                  data-lab-filter="all"
                  role="tab"
                  aria-selected="true">
            Tous
            <span>{{ $photos->count() }}</span>
          </button>
          @foreach($categories as $key => $meta)
            @if(($counts[$key] ?? 0) > 0)
              <button type="button"
                      class="laboratory-filter"
                      data-lab-filter="{{ $key }}"
                      role="tab"
                      aria-selected="false">
                <i class="{{ $meta['icon'] }}" aria-hidden="true"></i>
                {{ $meta['label'] }}
                <span>{{ $counts[$key] }}</span>
              </button>
            @endif
          @endforeach
        </div>
      </div>

      <div class="laboratory-grid">
        @foreach($photos as $index => $photo)
          @php $categoryMeta = $categories[$photo['category']] ?? $categories['equipe']; @endphp
          <article class="laboratory-card reveal"
                   data-lab-category="{{ $photo['category'] }}"
                   style="--lab-delay: {{ min($index * 0.05, 0.4) }}s">
            <button type="button"
                    class="laboratory-card__btn"
                    data-about-image
                    data-src="{{ e($photo['image_url']) }}"
                    data-title="{{ e($photo['title'] ?? '') }}"
                    data-caption="{{ e($photo['description'] ?? '') }}"
                    aria-label="Agrandir {{ $photo['title'] ?? 'photo' }}">
              <div class="laboratory-card__visual">
                <img src="{{ $photo['image_url'] }}"
                     alt="{{ $photo['title'] ?? 'Photo' }}"
                     loading="lazy"
                     decoding="async">
                <span class="laboratory-card__overlay" aria-hidden="true">
                  <i class="fas fa-expand"></i>
                </span>
              </div>
            </button>
            <div class="laboratory-card__body">
              <span class="laboratory-card__category">
                <i class="{{ $categoryMeta['icon'] }}" aria-hidden="true"></i>
                {{ $categoryMeta['label'] }}
              </span>
              <h2>{{ $photo['title'] ?? 'Photo' }}</h2>
              @if(filled($photo['description'] ?? null))
                <p>{{ $photo['description'] }}</p>
              @endif
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </section>
</main>

@include('accueil.partials.about-media-modals')

<script>
  (function () {
    const imageModal = document.getElementById('aboutImageModal');
    const imageEl = document.getElementById('aboutImageModalImg');
    const imageTitle = document.getElementById('aboutImageModalTitle');
    const imageCaption = document.getElementById('aboutImageModalCaption');
    let lastFocused = null;

    if (imageModal && imageModal.parentElement !== document.body) {
      document.body.appendChild(imageModal);
    }

    function lockBody(lock) {
      document.body.classList.toggle('about-modal-open', lock);
    }

    function closeImage() {
      if (!imageModal || imageModal.hidden) return;
      imageModal.hidden = true;
      imageModal.setAttribute('aria-hidden', 'true');
      if (imageEl) { imageEl.removeAttribute('src'); imageEl.alt = ''; }
      if (imageTitle) imageTitle.textContent = '';
      if (imageCaption) imageCaption.textContent = '';
      lockBody(false);
      lastFocused?.focus?.();
    }

    function openImage(src, title, caption) {
      if (!imageModal || !imageEl) return;
      lastFocused = document.activeElement;
      imageEl.src = src;
      imageEl.alt = title || 'Photo';
      if (imageTitle) imageTitle.textContent = title || '';
      if (imageCaption) imageCaption.textContent = caption || '';
      imageModal.hidden = false;
      imageModal.setAttribute('aria-hidden', 'false');
      lockBody(true);
      imageModal.querySelector('.about-modal__close')?.focus();
    }

    function filterLabPhotos(category) {
      document.querySelectorAll('[data-lab-filter]').forEach((tab) => {
        const isActive = tab.dataset.labFilter === category;
        tab.classList.toggle('is-active', isActive);
        tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      document.querySelectorAll('[data-lab-category]').forEach((card) => {
        const show = category === 'all' || card.dataset.labCategory === category;
        card.classList.toggle('is-filtered-out', !show);
        card.toggleAttribute('hidden', !show);
      });
    }

    document.addEventListener('click', (event) => {
      const filterBtn = event.target.closest('[data-lab-filter]');
      if (filterBtn) {
        event.preventDefault();
        filterLabPhotos(filterBtn.dataset.labFilter || 'all');
        return;
      }

      const imageBtn = event.target.closest('[data-about-image]');
      if (imageBtn) {
        event.preventDefault();
        openImage(imageBtn.dataset.src || '', imageBtn.dataset.title || '', imageBtn.dataset.caption || '');
        return;
      }

      if (event.target.closest('[data-about-modal-close]')) {
        closeImage();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && imageModal && !imageModal.hidden) {
        closeImage();
      }
    });
  })();
</script>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection
