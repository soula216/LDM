@extends('layouts.accueil')

@section('title', ($vosPatients['section_title'] ?? 'Vos patients') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;

    $categories = $categories ?? [];
    $categoryCounts = $categoryCounts ?? [];
    $videos = $videos ?? collect();
    $total = $videos->count();
@endphp

<main class="vos-patients-page">
  <section class="vos-patients-hero">
    <div class="vos-patients-hero__bg" aria-hidden="true"></div>
    <div class="vos-patients-hero__content">
      <div class="vos-patients-hero__badge">
        <i class="fas fa-play-circle" aria-hidden="true"></i>
        <span>{{ $vosPatients['section_label'] ?? 'Vos patients' }}</span>
      </div>
      <h1>{{ $vosPatients['section_title'] ?? 'Des sourires qui parlent' }}</h1>
      @if(filled($vosPatients['section_subtitle'] ?? null))
        <p>{{ $vosPatients['section_subtitle'] }}</p>
      @endif
      @if($total > 0)
        <div class="vos-patients-hero__stats">
          <div class="vos-patients-hero__stat">
            <strong>{{ $total }}</strong>
            <span>vidéo{{ $total > 1 ? 's' : '' }}</span>
          </div>
          <div class="vos-patients-hero__stat">
            <strong>{{ count($categoryCounts) }}</strong>
            <span>catégorie{{ count($categoryCounts) > 1 ? 's' : '' }}</span>
          </div>
        </div>
      @endif
    </div>
  </section>

  <section class="vos-patients-body" id="vos-patients">
    @if($total === 0)
      <div class="vos-patients-empty">
        <div class="vos-patients-empty__icon">
          <i class="fas fa-video" aria-hidden="true"></i>
        </div>
        <h2>Vidéos à venir</h2>
        <p>Les cas cliniques et témoignages patients seront bientôt disponibles ici.</p>
      </div>
    @else
      <div class="vos-patients-toolbar" data-vos-patients-filters>
        <div class="vos-patients-filters" role="tablist" aria-label="Filtrer par catégorie">
          <button type="button"
                  class="vos-patients-filter is-active"
                  data-filter="all"
                  role="tab"
                  aria-selected="true">
            Tous
            <span>{{ $total }}</span>
          </button>
          @foreach($categoryCounts as $categoryKey => $count)
            @php $meta = $categories[$categoryKey] ?? ['label' => ucfirst($categoryKey), 'icon' => '']; @endphp
            <button type="button"
                    class="vos-patients-filter"
                    data-filter="{{ $categoryKey }}"
                    role="tab"
                    aria-selected="false">
              @if(filled($meta['icon'] ?? null))
                <i class="{{ $meta['icon'] }}" aria-hidden="true"></i>
              @endif
              {{ $meta['label'] }}
              <span>{{ $count }}</span>
            </button>
          @endforeach
        </div>
      </div>

      <div class="vos-patients-grid" data-vos-patients-grid>
        @foreach($videos as $index => $video)
          @php
            $categoryKey = $video['category'] ?? '';
            $meta = $categories[$categoryKey] ?? ['label' => 'Vidéo', 'icon' => ''];
            $player = VitrineBlock::academyVideoPlayerConfig($video['video_url'] ?? '');
            $youtubeId = VitrineBlock::extractYouTubeVideoId($video['video_url'] ?? '');
            $thumb = $youtubeId
                ? 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg'
                : null;
          @endphp
          @if($player)
            <article class="vos-patients-card"
                     data-category="{{ $categoryKey }}"
                     style="--delay: {{ min($index, 8) * 40 }}ms">
              <button type="button"
                      class="vos-patients-card__media"
                      data-vos-patients-video
                      data-video-mode="{{ e($player['mode']) }}"
                      data-src="{{ e($player['src']) }}"
                      data-title="{{ e($video['title'] ?? 'Vidéo') }}"
                      aria-label="Lire : {{ $video['title'] ?? 'Vidéo' }}">
                @if($thumb)
                  <img src="{{ $thumb }}" alt="" loading="lazy" decoding="async" class="vos-patients-card__thumb">
                @else
                  <span class="vos-patients-card__thumb vos-patients-card__thumb--fallback" aria-hidden="true"></span>
                @endif
                <span class="vos-patients-card__play" aria-hidden="true">
                  <i class="fas fa-play"></i>
                </span>
              </button>

              <div class="vos-patients-card__body">
                @if(filled($meta['label'] ?? null))
                  <span class="vos-patients-card__category">
                    @if(filled($meta['icon'] ?? null))
                      <i class="{{ $meta['icon'] }}" aria-hidden="true"></i>
                    @endif
                    {{ $meta['label'] }}
                  </span>
                @endif
                @if(filled($video['title'] ?? null))
                  <h2>{{ $video['title'] }}</h2>
                @endif
                @if(filled($video['description'] ?? null))
                  <p>{{ $video['description'] }}</p>
                @endif
              </div>
            </article>
          @endif
        @endforeach
      </div>

      <div class="vos-patients-empty vos-patients-empty--filter" data-vos-patients-filter-empty hidden>
        <div class="vos-patients-empty__icon">
          <i class="fas fa-search" aria-hidden="true"></i>
        </div>
        <h2>Aucune vidéo dans cette catégorie</h2>
        <p>Sélectionnez une autre catégorie pour afficher les cas disponibles.</p>
      </div>
    @endif
  </section>
</main>

<div id="vosPatientsVideoModal"
     class="academy-media-modal academy-media-modal--video"
     hidden
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="vosPatientsVideoModalCaption">
  <div class="academy-media-modal__backdrop" data-vos-patients-modal-close></div>
  <div class="academy-media-modal__shell">
    <button type="button" class="academy-media-modal__close" data-vos-patients-modal-close aria-label="Fermer">
      <i class="fas fa-times" aria-hidden="true"></i>
    </button>
    <div class="academy-media-modal__video-wrap">
      <div id="vosPatientsVideoModalPlayer" class="academy-media-modal__player"></div>
      <p id="vosPatientsVideoModalCaption" class="academy-media-modal__video-title"></p>
    </div>
  </div>
</div>

<script>
  (function () {
    const modal = document.getElementById('vosPatientsVideoModal');
    const player = document.getElementById('vosPatientsVideoModalPlayer');
    const caption = document.getElementById('vosPatientsVideoModalCaption');
    const grid = document.querySelector('[data-vos-patients-grid]');
    const emptyFilter = document.querySelector('[data-vos-patients-filter-empty]');
    const filterButtons = document.querySelectorAll('[data-vos-patients-filters] [data-filter]');
    let lastFocused = null;

    if (modal && modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }

    function setBodyLock(active) {
      document.body.classList.toggle('academy-modal-open', active);
    }

    function closeModal() {
      if (!modal || modal.hidden) return;
      modal.hidden = true;
      modal.setAttribute('aria-hidden', 'true');
      if (player) player.innerHTML = '';
      if (caption) caption.textContent = '';
      setBodyLock(false);
      lastFocused?.focus?.();
    }

    function openModal(mode, src, title) {
      if (!modal || !player || !src) return;
      lastFocused = document.activeElement;
      player.innerHTML = '';

      if (mode === 'iframe') {
        const iframe = document.createElement('iframe');
        iframe.src = src;
        iframe.title = title || 'Vidéo';
        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
        iframe.allowFullscreen = true;
        iframe.setAttribute('frameborder', '0');
        player.appendChild(iframe);
      } else {
        const video = document.createElement('video');
        video.src = src;
        video.controls = true;
        video.autoplay = true;
        video.playsInline = true;
        player.appendChild(video);
      }

      if (caption) caption.textContent = title || '';
      modal.hidden = false;
      modal.setAttribute('aria-hidden', 'false');
      setBodyLock(true);
      modal.querySelector('.academy-media-modal__close')?.focus();
    }

    document.addEventListener('click', (event) => {
      const trigger = event.target.closest('[data-vos-patients-video]');
      if (trigger) {
        openModal(
          trigger.getAttribute('data-video-mode') || 'video',
          trigger.getAttribute('data-src') || '',
          trigger.getAttribute('data-title') || ''
        );
        return;
      }

      if (event.target.closest('[data-vos-patients-modal-close]')) {
        closeModal();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') closeModal();
    });

    function applyFilter(filter) {
      if (!grid) return;
      let visible = 0;

      grid.querySelectorAll('.vos-patients-card').forEach((card) => {
        const match = filter === 'all' || card.getAttribute('data-category') === filter;
        card.hidden = !match;
        if (match) visible += 1;
      });

      if (emptyFilter) emptyFilter.hidden = visible > 0;
    }

    filterButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        const filter = btn.getAttribute('data-filter') || 'all';
        filterButtons.forEach((other) => {
          const active = other === btn;
          other.classList.toggle('is-active', active);
          other.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        applyFilter(filter);
      });
    });
  })();
</script>
@endsection
