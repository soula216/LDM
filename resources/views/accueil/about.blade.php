@extends('layouts.accueil')

@section('title', ($about['title'] ?? 'Le Laboratoire') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;

    $photos = VitrineBlock::aboutPhotos($about);
    $videos = VitrineBlock::aboutVideos($about);
    $sections = VitrineBlock::aboutSections($about);
    $hasMedia = $photos->isNotEmpty() || $videos->isNotEmpty();
    $hasDescription = filled($about['description'] ?? null);
    $hasSections = $sections->isNotEmpty();

    $sectionsKicker = trim((string) ($about['sections_kicker'] ?? ''));
    $sectionsHeading = trim((string) ($about['sections_heading'] ?? ''));
    $sectionsLead = trim((string) ($about['sections_lead'] ?? ''));

    if ($hasSections) {
        $sectionsKicker = $sectionsKicker !== '' ? $sectionsKicker : 'En détail';
        $sectionsHeading = $sectionsHeading !== '' ? $sectionsHeading : 'Nos engagements & expertises';
        $sectionsLead = $sectionsLead !== '' ? $sectionsLead : 'Découvrez les piliers qui structurent notre laboratoire et notre relation avec les praticiens.';
    }

    $hasSectionsBlock = $hasSections
        || $sectionsKicker !== ''
        || $sectionsHeading !== ''
        || $sectionsLead !== '';
    $hasBothMediaTypes = $photos->isNotEmpty() && $videos->isNotEmpty();
    $mediaIndex = 0;
@endphp

<main class="about-page">
  <section class="about-hero">
    <div class="about-hero__bg" aria-hidden="true"></div>
    <div class="about-hero__mesh" aria-hidden="true"></div>
    <div class="about-hero__content">
      <div class="about-hero__badge">
        <i class="fas fa-building" aria-hidden="true"></i>
        <span>{{ $about['section_label'] ?? 'À propos' }}</span>
      </div>
      <h1>{{ $about['title'] ?? 'Notre laboratoire' }}</h1>
      <div class="about-hero__line" aria-hidden="true"></div>
    </div>
  </section>

  <section class="about-body">
    <div class="about-layout @if(!$hasMedia) about-layout--solo @endif">
      <article class="about-main reveal">
        <div class="about-main__card">
          <header class="about-main__head">
            <span class="about-main__eyebrow">Présentation</span>
            @if($hasMedia)
              <div class="about-main__meta">
                @if($photos->isNotEmpty())
                  <span><i class="fas fa-image" aria-hidden="true"></i> {{ $photos->count() }} photo{{ $photos->count() > 1 ? 's' : '' }}</span>
                @endif
                @if($videos->isNotEmpty())
                  <span><i class="fas fa-play-circle" aria-hidden="true"></i> {{ $videos->count() }} vidéo{{ $videos->count() > 1 ? 's' : '' }}</span>
                @endif
              </div>
            @endif
          </header>

          @if($hasDescription)
            <div class="about-main__content">
              @foreach(preg_split('/\R{2,}/', trim($about['description'])) as $paragraph)
                @if(filled($paragraph))
                  <p>{{ $paragraph }}</p>
                @endif
              @endforeach
            </div>
          @else
            <div class="about-main__empty">
              <p>Le contenu de présentation sera bientôt disponible.</p>
            </div>
          @endif
        </div>
      </article>

      @if($hasMedia)
        <aside class="about-sidebar reveal" style="--about-delay: 0.12s">
          <div class="about-sidebar__card">
            <header class="about-sidebar__head">
              <div>
                <span class="about-sidebar__eyebrow">Médias</span>
                <h2>Galerie & vidéos</h2>
              </div>
              @if($hasBothMediaTypes)
                <div class="about-sidebar__tabs" role="tablist" aria-label="Filtrer les médias">
                  <button type="button"
                          class="about-sidebar__tab is-active"
                          data-about-filter="all"
                          role="tab"
                          aria-selected="true">
                    Tous
                  </button>
                  <button type="button"
                          class="about-sidebar__tab"
                          data-about-filter="photo"
                          role="tab"
                          aria-selected="false">
                    <i class="fas fa-image" aria-hidden="true"></i>
                    Images
                  </button>
                  <button type="button"
                          class="about-sidebar__tab"
                          data-about-filter="video"
                          role="tab"
                          aria-selected="false">
                    <i class="fas fa-play" aria-hidden="true"></i>
                    Vidéos
                  </button>
                </div>
              @endif
            </header>

            <div class="about-sidebar__mosaic" data-about-mosaic>
              @foreach($photos as $photo)
                <button type="button"
                        class="about-sidebar__tile about-sidebar__tile--photo"
                        data-about-media-type="photo"
                        data-about-image
                        data-src="{{ e($photo['image_url']) }}"
                        data-title="{{ e($photo['title'] ?? '') }}"
                        data-caption="{{ e($photo['caption'] ?? '') }}"
                        aria-label="Agrandir {{ $photo['title'] ?? 'photo' }}"
                        style="--about-media-delay: {{ min($mediaIndex * 0.05, 0.4) }}s">
                  <img src="{{ $photo['image_url'] }}"
                       alt="{{ $photo['title'] ?? 'Photo' }}"
                       loading="lazy"
                       decoding="async">
                  <span class="about-sidebar__tile-overlay" aria-hidden="true">
                    <i class="fas fa-expand"></i>
                  </span>
                  @if(filled($photo['title'] ?? null))
                    <span class="about-sidebar__tile-label">{{ $photo['title'] }}</span>
                  @endif
                </button>
                @php $mediaIndex++; @endphp
              @endforeach

              @foreach($videos as $video)
                @php
                  $videoConfig = VitrineBlock::academyVideoPlayerConfig($video['video_url'] ?? '');
                  $posterUrl = VitrineBlock::aboutVideoPosterUrl($video);
                @endphp
                @if($videoConfig)
                  <button type="button"
                          class="about-sidebar__tile about-sidebar__tile--video"
                          data-about-media-type="video"
                          data-about-video
                          data-video-mode="{{ e($videoConfig['mode']) }}"
                          data-src="{{ e($videoConfig['src']) }}"
                          data-title="{{ e($video['title'] ?? 'Vidéo') }}"
                          aria-label="Lire {{ $video['title'] ?? 'la vidéo' }}"
                          style="--about-media-delay: {{ min($mediaIndex * 0.05, 0.4) }}s">
                    <span class="about-sidebar__tile-visual" @if($posterUrl) style="background-image: url('{{ e($posterUrl) }}')" @endif></span>
                    <span class="about-sidebar__tile-overlay about-sidebar__tile-overlay--video" aria-hidden="true">
                      <i class="fas fa-play"></i>
                    </span>
                    @if(filled($video['title'] ?? null))
                      <span class="about-sidebar__tile-label">{{ $video['title'] }}</span>
                    @else
                      <span class="about-sidebar__tile-label">Vidéo</span>
                    @endif
                  </button>
                  @php $mediaIndex++; @endphp
                @endif
              @endforeach
            </div>
          </div>
        </aside>
      @endif
    </div>

    @if($hasSectionsBlock)
      <section class="about-sections reveal" aria-label="Sections complémentaires">
        @if($sectionsKicker !== '' || $sectionsHeading !== '' || $sectionsLead !== '')
          <div class="about-sections__head">
            @if($sectionsKicker !== '')
              <span class="about-sections__kicker">{{ $sectionsKicker }}</span>
            @endif
            @if($sectionsHeading !== '')
              <h2 class="about-sections__heading">{{ $sectionsHeading }}</h2>
            @endif
            @if($sectionsLead !== '')
              <p class="about-sections__lead">{{ $sectionsLead }}</p>
            @endif
          </div>
        @endif

        @if($hasSections)
        <div class="about-sections__track">
          @foreach($sections as $index => $section)
            <article class="about-sections__item @if($index % 2 === 1) about-sections__item--alt @endif reveal"
                     style="--about-section-delay: {{ min($index * 0.08, 0.48) }}s">
              <div class="about-sections__index" aria-hidden="true">
                <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
              </div>
              <div class="about-sections__body">
                @if(filled($section['title']))
                  <h3 class="about-sections__title">{{ $section['title'] }}</h3>
                @endif
                @if(filled($section['description']))
                  <div class="about-sections__content">
                    @foreach(preg_split('/\R{2,}/', trim($section['description'])) as $paragraph)
                      @if(filled($paragraph))
                        <p>{{ $paragraph }}</p>
                      @endif
                    @endforeach
                  </div>
                @endif
              </div>
              <div class="about-sections__glow" aria-hidden="true"></div>
            </article>
          @endforeach
        </div>
        @endif
      </section>
    @endif

    @if(!$hasMedia && !$hasDescription && !$hasSectionsBlock)
      <div class="about-empty reveal">
        <div class="about-empty__icon"><i class="fas fa-info-circle" aria-hidden="true"></i></div>
        <h2>Contenu à venir</h2>
        <p>La page À propos sera bientôt enrichie.</p>
      </div>
    @endif
  </section>
</main>

@include('accueil.partials.about-media-modals')

<script>
  (function () {
    const imageModal = document.getElementById('aboutImageModal');
    const videoModal = document.getElementById('aboutVideoModal');
    const imageEl = document.getElementById('aboutImageModalImg');
    const imageTitle = document.getElementById('aboutImageModalTitle');
    const imageCaption = document.getElementById('aboutImageModalCaption');
    const videoPlayer = document.getElementById('aboutVideoModalPlayer');
    const videoTitle = document.getElementById('aboutVideoModalTitle');
    let lastFocused = null;

    [imageModal, videoModal].forEach((modal) => {
      if (modal && modal.parentElement !== document.body) document.body.appendChild(modal);
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
        iframe.title = title || 'Vidéo';
        iframe.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture; fullscreen');
        iframe.allowFullscreen = true;
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

    function filterAboutMedia(filter) {
      document.querySelectorAll('[data-about-filter]').forEach((tab) => {
        const isActive = tab.dataset.aboutFilter === filter;
        tab.classList.toggle('is-active', isActive);
        tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      document.querySelectorAll('[data-about-media-type]').forEach((item) => {
        const type = item.dataset.aboutMediaType;
        const show = filter === 'all' || filter === type;
        item.classList.toggle('is-filtered-out', !show);
        item.toggleAttribute('hidden', !show);
      });
    }

    document.addEventListener('click', (event) => {
      const filterBtn = event.target.closest('[data-about-filter]');
      if (filterBtn) {
        event.preventDefault();
        filterAboutMedia(filterBtn.dataset.aboutFilter || 'all');
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
        if (imageModal && !imageModal.hidden) closeImage();
        if (videoModal && !videoModal.hidden) closeVideo();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key !== 'Escape') return;
      if (imageModal && !imageModal.hidden) closeImage();
      else if (videoModal && !videoModal.hidden) closeVideo();
    });
  })();
</script>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection
