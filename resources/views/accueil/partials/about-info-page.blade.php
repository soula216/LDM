@php
    $infoPage = $infoPage ?? null;
    $title = trim((string) ($infoPage['title'] ?? ''));
    $contentHtml = trim((string) ($infoPage['content_html'] ?? ''));
    $hasContent = $title !== '' || $contentHtml !== '';
@endphp

@if($hasContent)
  <article class="about-article reveal" aria-label="{{ $title !== '' ? $title : 'Page' }}">
    <div class="about-article__card">
      @if($title !== '')
        <header class="about-article__head">
          <span class="about-article__eyebrow">Le Laboratoire</span>
          <h2 class="about-article__title">{{ $title }}</h2>
        </header>
      @endif

      @if($contentHtml !== '')
        <div class="about-article__content prose-vitrine process-step-detail-modal-content">
          {!! $contentHtml !!}
        </div>
      @endif
    </div>
  </article>
@else
  <div class="about-empty reveal">
    <div class="about-empty__icon"><i class="fas fa-info-circle" aria-hidden="true"></i></div>
    <h2>{{ $emptyTitle ?? 'Contenu à venir' }}</h2>
    <p>{{ $emptyText ?? 'Cette page sera bientôt disponible.' }}</p>
  </div>
@endif
