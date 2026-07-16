@extends('layouts.accueil')

@section('title', ($laboratory['title'] ?? 'Galerie') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;

    $mediaItems = VitrineBlock::laboratoryMediaItems($laboratory);
    $photos = $mediaItems->where('type', 'image')->values();
    $videos = $mediaItems->where('type', 'video')->values();
    $categories = VitrineBlock::laboratoryCategories();
    $counts = $mediaItems->countBy('category');
    $hasMedia = $mediaItems->isNotEmpty();
@endphp

<main class="laboratory-page">
  <section class="laboratory-hero">
    <div class="laboratory-hero__bg" aria-hidden="true"></div>
    <div class="laboratory-hero__mesh" aria-hidden="true"></div>
    <div class="laboratory-hero__content">
      <div class="laboratory-hero__badge">
        <i class="fas fa-images" aria-hidden="true"></i>
        <span>{{ $laboratory['section_label'] ?? 'Galerie' }}</span>
      </div>
      <h1>{{ $laboratory['title'] ?? 'Notre équipe & nos installations' }}</h1>
      @if(filled($laboratory['description'] ?? null))
        <p class="laboratory-hero__lead">{{ $laboratory['description'] }}</p>
      @endif
      @if($hasMedia)
        <div class="laboratory-hero__stats">
          @if($photos->isNotEmpty())
            <div class="laboratory-hero__stat">
              <strong>{{ $photos->count() }}</strong>
              <span>photo{{ $photos->count() > 1 ? 's' : '' }}</span>
            </div>
          @endif
          @if($videos->isNotEmpty())
            <div class="laboratory-hero__stat">
              <strong>{{ $videos->count() }}</strong>
              <span>vidéo{{ $videos->count() > 1 ? 's' : '' }}</span>
            </div>
          @endif
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
    @if(! $hasMedia)
      <div class="laboratory-empty reveal">
        <div class="laboratory-empty__icon"><i class="fas fa-camera" aria-hidden="true"></i></div>
        <h2>Galerie à venir</h2>
        <p>Les photos et vidéos de l'équipe, du laboratoire et des équipements seront bientôt disponibles.</p>
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
            <span>{{ $mediaItems->count() }}</span>
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
        @foreach($mediaItems as $index => $item)
          @php
            $categoryMeta = $categories[$item['category']] ?? $categories['equipe'];
            $delay = min($index * 0.05, 0.4);
            $isVideo = ($item['type'] ?? '') === 'video';
            $videoConfig = $isVideo ? VitrineBlock::academyVideoPlayerConfig($item['video_url'] ?? '') : null;
            $posterUrl = $isVideo ? VitrineBlock::aboutVideoPosterUrl($item) : '';
          @endphp

          @if($isVideo && $videoConfig)
            <article class="laboratory-card laboratory-card--video reveal"
                     data-lab-category="{{ $item['category'] }}"
                     style="--lab-delay: {{ $delay }}s">
              <button type="button"
                      class="laboratory-card__btn"
                      data-about-video
                      data-video-mode="{{ $videoConfig['mode'] }}"
                      data-src="{{ e($videoConfig['src']) }}"
                      data-title="{{ e($item['title'] ?? '') }}"
                      aria-label="Lire {{ $item['title'] ?? 'vidéo' }}">
                <div class="laboratory-card__visual">
                  @if(filled($posterUrl))
                    <img src="{{ $posterUrl }}"
                         alt="{{ $item['title'] ?? 'Vidéo' }}"
                         loading="lazy"
                         decoding="async">
                  @else
                    <span class="laboratory-card__visual-fallback" aria-hidden="true"></span>
                  @endif
                  <span class="laboratory-card__overlay laboratory-card__overlay--video" aria-hidden="true">
                    <i class="fas fa-play"></i>
                  </span>
                </div>
              </button>
              <div class="laboratory-card__body">
                <span class="laboratory-card__category">
                  <i class="fas fa-play-circle" aria-hidden="true"></i>
                  Vidéo · {{ $categoryMeta['label'] }}
                </span>
                <h2>{{ $item['title'] ?? 'Vidéo' }}</h2>
                @if(filled($item['description'] ?? null))
                  <p>{{ $item['description'] }}</p>
                @endif
              </div>
            </article>
          @elseif(! $isVideo)
            <article class="laboratory-card reveal"
                     data-lab-category="{{ $item['category'] }}"
                     style="--lab-delay: {{ $delay }}s">
              <button type="button"
                      class="laboratory-card__btn"
                      data-about-image
                      data-src="{{ e($item['image_url']) }}"
                      data-title="{{ e($item['title'] ?? '') }}"
                      data-caption="{{ e($item['description'] ?? '') }}"
                      aria-label="Agrandir {{ $item['title'] ?? 'photo' }}">
                <div class="laboratory-card__visual">
                  <img src="{{ $item['image_url'] }}"
                       alt="{{ $item['title'] ?? 'Photo' }}"
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
                <h2>{{ $item['title'] ?? 'Photo' }}</h2>
                @if(filled($item['description'] ?? null))
                  <p>{{ $item['description'] }}</p>
                @endif
              </div>
            </article>
          @endif
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
    const videoModal = document.getElementById('aboutVideoModal');
    const videoPlayer = document.getElementById('aboutVideoModalPlayer');
    const videoTitle = document.getElementById('aboutVideoModalTitle');
    let lastFocused = null;

    [imageModal, videoModal].forEach((modal) => {
      if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
      }
    });

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
      if (videoModal?.hidden) lockBody(false);
      lastFocused?.focus?.();
    }

    function closeVideo() {
      if (!videoModal || videoModal.hidden) return;
      videoModal.hidden = true;
      videoModal.setAttribute('aria-hidden', 'true');
      if (videoPlayer) videoPlayer.innerHTML = '';
      if (videoTitle) videoTitle.textContent = '';
      if (imageModal?.hidden) lockBody(false);
      lastFocused?.focus?.();
    }

    function openImage(src, title, caption) {
      if (!imageModal || !imageEl) return;
      closeVideo();
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

    function openVideo(mode, src, title) {
      if (!videoModal || !videoPlayer || !src) return;
      closeImage();
      lastFocused = document.activeElement;
      videoPlayer.innerHTML = '';

      if (mode === 'iframe') {
        const iframe = document.createElement('iframe');
        iframe.src = src;
        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
        iframe.allowFullscreen = true;
        iframe.title = title || 'Vidéo';
        videoPlayer.appendChild(iframe);
      } else {
        const video = document.createElement('video');
        video.src = src;
        video.controls = true;
        video.autoplay = true;
        video.playsInline = true;
        videoPlayer.appendChild(video);
      }

      if (videoTitle) videoTitle.textContent = title || '';
      videoModal.hidden = false;
      videoModal.setAttribute('aria-hidden', 'false');
      lockBody(true);
      videoModal.querySelector('.about-modal__close')?.focus();
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

      const videoBtn = event.target.closest('[data-about-video]');
      if (videoBtn) {
        event.preventDefault();
        openVideo(videoBtn.dataset.videoMode || 'video', videoBtn.dataset.src || '', videoBtn.dataset.title || '');
        return;
      }

      if (event.target.closest('[data-about-modal-close]')) {
        if (videoModal && !videoModal.hidden) closeVideo();
        else closeImage();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key !== 'Escape') return;
      if (videoModal && !videoModal.hidden) closeVideo();
      else if (imageModal && !imageModal.hidden) closeImage();
    });
  })();
</script>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection
