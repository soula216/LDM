@php
    $section = $section ?? null;
    $hasSection = is_array($section) && (filled($section['title'] ?? null) || filled($section['description'] ?? null));
    $title = trim((string) ($section['title'] ?? ''));
    $description = trim((string) ($section['description'] ?? ''));
    $paragraphs = $description !== ''
        ? array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', $description) ?: [])))
        : [];
@endphp

@if($hasSection)
  <article class="about-article reveal" aria-label="{{ $title !== '' ? $title : 'Section' }}">
    <div class="about-article__card">
      @if($title !== '')
        <header class="about-article__head">
          <span class="about-article__eyebrow">Le Laboratoire</span>
          <h2 class="about-article__title">{{ $title }}</h2>
        </header>
      @endif

      @if($paragraphs !== [])
        <div class="about-article__content">
          @foreach($paragraphs as $paragraph)
            <p>{{ $paragraph }}</p>
          @endforeach
        </div>
      @endif
    </div>
  </article>
@else
  <div class="about-empty reveal">
    <div class="about-empty__icon"><i class="fas fa-info-circle" aria-hidden="true"></i></div>
    <h2>{{ $emptyTitle ?? 'Contenu à venir' }}</h2>
    <p>{{ $emptyText ?? 'Cette section sera bientôt disponible.' }}</p>
  </div>
@endif
