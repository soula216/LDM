@extends('layouts.accueil')

@php
    use App\Models\VitrineBlock;

    $aboutPage = $aboutPage ?? 'qui-sommes-nous';
    $isOverviewPage = VitrineBlock::isAboutOverviewPage($aboutPage);
    $aboutTabs = VitrineBlock::aboutOverviewTabs();
    $activeAboutTab = $activeAboutTab ?? 'qui-sommes-nous';
    $isWorkPage = VitrineBlock::isAboutWorkPage($aboutPage);
    $workTabs = VitrineBlock::aboutWorkTabs();
    $activeWorkTab = $activeWorkTab ?? 'processus-de-qualite';
    $isCollaborationPage = VitrineBlock::isAboutCollaborationPage($aboutPage);
    $collaborationTabs = VitrineBlock::aboutCollaborationTabs();
    $activeCollaborationTab = $activeCollaborationTab ?? 'services';
    $contentPage = match (true) {
        $isWorkPage => $activeWorkTab,
        $isCollaborationPage => $activeCollaborationTab,
        default => $aboutPage,
    };
    $subPages = VitrineBlock::orderedAboutSubPages($about);
    $pageLabel = match (true) {
        $isOverviewPage => VitrineBlock::aboutOverviewPageLabel(),
        $isWorkPage => VitrineBlock::aboutWorkPageLabel(),
        $isCollaborationPage => VitrineBlock::aboutCollaborationPageLabel(),
        default => $subPages[$contentPage]['label'] ?? 'Le Laboratoire',
    };
    $isInfoPage = VitrineBlock::isAboutInfoPage($contentPage);
    $isMediaPage = VitrineBlock::isAboutMediaPage($contentPage);
    $infoPage = $isInfoPage ? VitrineBlock::aboutInfoPage($about, $contentPage) : null;
    $mediaPage = $isMediaPage ? VitrineBlock::aboutMediaPage($about) : null;
    $mediaPhotos = $isMediaPage ? VitrineBlock::aboutMediaPagePhotos($about) : collect();

    $photos = VitrineBlock::aboutPhotos($about);
    $videos = VitrineBlock::aboutVideos($about);
    $mission = VitrineBlock::aboutMissionSection($about);
    $principles = VitrineBlock::aboutPrinciplesSection($about);
    $hasMedia = $photos->isNotEmpty() || $videos->isNotEmpty();
    $hasDescription = filled($about['description'] ?? null);
    $hasBothMediaTypes = $photos->isNotEmpty() && $videos->isNotEmpty();
    $mediaIndex = 0;
    $isMissionTab = $isOverviewPage && $activeAboutTab === 'notre-mission';
    $isPrinciplesTab = $isOverviewPage && $activeAboutTab === 'nos-principe';
    $isCertificationsTab = $isWorkPage && $activeWorkTab === VitrineBlock::aboutMediaPageSlug();
    $isWarrantyTab = $isWorkPage && $activeWorkTab === 'garantie';
    $isServicesTab = $isCollaborationPage && $activeCollaborationTab === 'services';
    $isProcessTab = $isCollaborationPage && $activeCollaborationTab === VitrineBlock::aboutProcessPageSlug();

    $heroBadge = match (true) {
        $isMissionTab => filled($about['sections_kicker'] ?? null)
            ? $about['sections_kicker']
            : 'En détail',
        $isPrinciplesTab => filled($about['principles_kicker'] ?? null)
            ? $about['principles_kicker']
            : 'Nos valeurs',
        $isCertificationsTab => filled($about['certifications_kicker'] ?? null)
            ? $about['certifications_kicker']
            : 'Qualité certifiée',
        $isWarrantyTab => filled($infoPage['hero_kicker'] ?? null)
            ? $infoPage['hero_kicker']
            : 'Votre sérénité',
        $isServicesTab => $services['section_label'] ?? 'Nos Services',
        $isProcessTab => $process['section_label'] ?? 'Notre Process',
        $isCollaborationPage => VitrineBlock::aboutCollaborationPageLabel(),
        $isWorkPage => VitrineBlock::aboutWorkPageLabel(),
        $isMediaPage => $mediaPage['section_label'] ?? 'Certifications',
        $isOverviewPage => VitrineBlock::aboutOverviewPageLabel(),
        default => $about['section_label'] ?? 'Le Laboratoire',
    };

    $heroTitle = match (true) {
        $isMissionTab => filled($about['sections_heading'] ?? null)
            ? $about['sections_heading']
            : ($mission['title'] ?? 'Notre mission'),
        $isPrinciplesTab => filled($about['principles_heading'] ?? null)
            ? $about['principles_heading']
            : ($principles['title'] ?? 'Nos principe'),
        $isCertificationsTab => filled($about['certifications_heading'] ?? null)
            ? $about['certifications_heading']
            : ($mediaPage['title'] ?? 'Certifications'),
        $isWarrantyTab => filled($infoPage['hero_heading'] ?? null)
            ? $infoPage['hero_heading']
            : ($infoPage['title'] ?? 'Garantie'),
        $isServicesTab => $services['section_title'] ?? 'Solutions Complètes',
        $isProcessTab => $process['section_title'] ?? 'Comment Nous Travaillons',
        $isCollaborationPage && $isInfoPage => $infoPage['title'] ?? $pageLabel,
        $isWorkPage && $isInfoPage => $infoPage['title'] ?? $pageLabel,
        $isWorkPage && $isMediaPage => $mediaPage['title'] ?? $pageLabel,
        $isOverviewPage => $about['title'] ?? 'À propos de LDM',
        $aboutPage === 'notre-mission' => $mission['title'] ?? 'Notre mission',
        $aboutPage === 'nos-principe' => $principles['title'] ?? 'Nos principe',
        $isInfoPage => $infoPage['title'] ?? $pageLabel,
        $isMediaPage => $mediaPage['title'] ?? $pageLabel,
        default => $about['title'] ?? 'Notre laboratoire',
    };

    $heroDescription = match (true) {
        $isMissionTab => trim((string) ($about['sections_lead'] ?? '')),
        $isPrinciplesTab => trim((string) ($about['principles_lead'] ?? '')),
        $isCertificationsTab => trim((string) ($about['certifications_lead'] ?? '')),
        $isWarrantyTab => trim((string) ($infoPage['hero_lead'] ?? '')),
        $isServicesTab => trim((string) ($services['section_subtitle'] ?? '')),
        $isProcessTab => trim((string) ($process['section_subtitle'] ?? '')),
        default => '',
    };

    $heroIcon = match (true) {
        $isWorkPage && $activeWorkTab === 'processus-de-qualite' => 'fa-list-check',
        $isWorkPage && $activeWorkTab === VitrineBlock::aboutMediaPageSlug() => 'fa-certificate',
        $isWorkPage && $activeWorkTab === 'garantie' => 'fa-shield-alt',
        $isServicesTab => 'fa-tooth',
        $isProcessTab => 'fa-project-diagram',
        $isCollaborationPage => 'fa-handshake',
        $isMediaPage => 'fa-images',
        $isOverviewPage && $activeAboutTab === 'notre-mission' => 'fa-bullseye',
        $isOverviewPage && $activeAboutTab === 'nos-principe' => 'fa-compass',
        $isOverviewPage => 'fa-users',
        default => 'fa-building',
    };

    $documentTitle = match (true) {
        $isOverviewPage => VitrineBlock::aboutOverviewPageLabel(),
        $isWorkPage => VitrineBlock::aboutWorkPageLabel(),
        $isCollaborationPage => $collaborationTabs[$activeCollaborationTab]['label']
            ?? VitrineBlock::aboutCollaborationPageLabel(),
        $isInfoPage => $infoPage['title'] ?? $pageLabel,
        $isMediaPage => $mediaPage['title'] ?? $pageLabel,
        default => $pageLabel,
    };
@endphp

@section('title', $documentTitle . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
<main class="about-page">
  <section class="about-hero">
    <div class="about-hero__bg" aria-hidden="true"></div>
    <div class="about-hero__mesh" aria-hidden="true"></div>
    <div class="about-hero__content">
      <div class="about-hero__badge">
        <i class="fas {{ $heroIcon }}" aria-hidden="true"></i>
        <span>{{ $heroBadge }}</span>
      </div>
      <h1>{{ $heroTitle }}</h1>
      @if(filled($heroDescription))
        <p class="about-hero__description">{{ $heroDescription }}</p>
      @endif
      <div class="about-hero__line" aria-hidden="true"></div>
    </div>
  </section>

  <section @class([
      'about-body',
      'about-body--standalone' => $aboutPage !== 'qui-sommes-nous' && ! $isOverviewPage && ! $isWorkPage && ! $isCollaborationPage,
  ])>
    @if($isOverviewPage)
      <nav class="about-overview-tabs reveal" role="tablist" aria-label="Rubriques À propos">
        @foreach($aboutTabs as $tabKey => $tab)
          <a href="{{ route('vitrine.about.show', ['page' => VitrineBlock::aboutOverviewPageSlug(), 'tab' => $tabKey]) }}"
             role="tab"
             aria-selected="{{ $activeAboutTab === $tabKey ? 'true' : 'false' }}"
             @class(['about-overview-tab', 'is-active' => $activeAboutTab === $tabKey])>
            <span class="about-overview-tab__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <span>{{ $tab['label'] }}</span>
          </a>
        @endforeach
      </nav>
    @endif

    @if($isWorkPage)
      <nav class="about-overview-tabs reveal" role="tablist" aria-label="Rubriques Qualité et Certifications">
        @foreach($workTabs as $tabKey => $tab)
          <a href="{{ route('vitrine.about.show', ['page' => VitrineBlock::aboutWorkPageSlug(), 'tab' => $tabKey]) }}"
             role="tab"
             aria-selected="{{ $activeWorkTab === $tabKey ? 'true' : 'false' }}"
             @class(['about-overview-tab', 'is-active' => $activeWorkTab === $tabKey])>
            <span class="about-overview-tab__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <span>{{ $tab['label'] }}</span>
          </a>
        @endforeach
      </nav>
    @endif

    @if($isCollaborationPage)
      <nav class="about-overview-tabs about-overview-tabs--wide reveal" role="tablist" aria-label="Rubriques Travailler avec LDM">
        @foreach($collaborationTabs as $tabKey => $tab)
          <a href="{{ route('vitrine.about.show', ['page' => VitrineBlock::aboutCollaborationPageSlug(), 'tab' => $tabKey]) }}"
             role="tab"
             aria-selected="{{ $activeCollaborationTab === $tabKey ? 'true' : 'false' }}"
             @class(['about-overview-tab', 'is-active' => $activeCollaborationTab === $tabKey])>
            <span class="about-overview-tab__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <span>{{ $tab['label'] }}</span>
          </a>
        @endforeach
      </nav>
    @endif

    @if($aboutPage === 'qui-sommes-nous' || ($isOverviewPage && $activeAboutTab === 'qui-sommes-nous'))
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
                            data-video-mode="{{ $videoConfig['mode'] }}"
                            data-src="{{ e($videoConfig['src']) }}"
                            data-title="{{ e($video['title'] ?? '') }}"
                            aria-label="Lire {{ $video['title'] ?? 'vidéo' }}"
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

      @if(!$hasMedia && !$hasDescription)
        <div class="about-empty reveal">
          <div class="about-empty__icon"><i class="fas fa-info-circle" aria-hidden="true"></i></div>
          <h2>Contenu à venir</h2>
          <p>La présentation du laboratoire sera bientôt disponible.</p>
        </div>
      @endif

    @elseif($aboutPage === 'notre-mission' || ($isOverviewPage && $activeAboutTab === 'notre-mission'))
      @include('accueil.partials.about-section-page', [
          'section' => $mission,
          'emptyTitle' => 'Mission à venir',
          'emptyText' => 'Notre mission sera bientôt détaillée ici.',
      ])

    @elseif($aboutPage === 'nos-principe' || ($isOverviewPage && $activeAboutTab === 'nos-principe'))
      @include('accueil.partials.about-section-page', [
          'section' => $principles,
          'emptyTitle' => 'Principes à venir',
          'emptyText' => 'Nos principes seront bientôt détaillés ici.',
      ])

    @elseif($isServicesTab)
      @include('accueil.partials.about-services-page', ['services' => $services])

    @elseif($isProcessTab)
      @include('accueil.partials.about-process-page', ['process' => $process])

    @elseif($isInfoPage)
      @include('accueil.partials.about-info-page', [
          'infoPage' => $infoPage,
          'emptyTitle' => 'Contenu à venir',
          'emptyText' => 'Cette page sera bientôt disponible.',
      ])

    @elseif($isMediaPage)
      @include('accueil.partials.about-media-page', [
          'about' => $about,
          'mediaPage' => $mediaPage,
          'mediaPhotos' => $mediaPhotos,
      ])
    @endif
  </section>
</main>

@if($aboutPage === 'qui-sommes-nous' || $isOverviewPage || $isMediaPage)
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
@endif
@endsection

@section('footer')
  @include('accueil.footer')
@endsection
