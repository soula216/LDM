@extends('layouts.accueil')

@section('title', ($process['section_title'] ?? 'Processus') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;

    $steps = VitrineBlock::activeProcessSteps($process['steps'] ?? []);
    $defaultIcons = ['fa-comments', 'fa-cube', 'fa-cogs', 'fa-truck-fast'];
@endphp

<main class="inner-page">
  <section class="inner-hero">
    <div class="inner-hero-bg" aria-hidden="true"></div>
    <div class="inner-hero-content">
      <div class="inner-hero-badge">
        <i class="fas fa-project-diagram" aria-hidden="true"></i>
        <span>{{ $process['section_label'] ?? 'Notre Process' }}</span>
      </div>
      <h1>{{ $process['section_title'] ?? 'Comment Nous Travaillons' }}</h1>
      @if(filled($process['section_subtitle'] ?? null))
        <p>{{ $process['section_subtitle'] }}</p>
      @endif
    </div>
  </section>

  <section class="inner-body process process--page" id="process">
    @if($steps->isEmpty())
      <div class="inner-empty">
        <div class="inner-empty-icon"><i class="fas fa-stream" aria-hidden="true"></i></div>
        <h2>Processus à venir</h2>
        <p>Les étapes de notre méthode de travail seront bientôt présentées ici.</p>
      </div>
    @else
      <div class="process-timeline-wrap">
        <div class="process-timeline-header">
          <span class="process-timeline-count">{{ $steps->count() }} étape{{ $steps->count() > 1 ? 's' : '' }}</span>
          <p>Un parcours structuré, de la prise de contact à la livraison finale.</p>
        </div>

        <div class="process-timeline">
          @foreach($steps as $step)
            @php
              $icon = filled($step['icon'] ?? null)
                ? $step['icon']
                : ($defaultIcons[$loop->index % count($defaultIcons)] ?? 'fa-circle-check');
              $stepTitle = trim((string) ($step['title'] ?? ''));
              $hasDetailHtml = filled($step['detail_html'] ?? null);
            @endphp
            <article class="process-timeline-item reveal active">
              <div class="process-timeline-marker" aria-hidden="true">
                <span class="process-timeline-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
              </div>
              <div class="process-timeline-card">
                <div class="process-timeline-card-top">
                  <div class="process-timeline-icon">
                    <i class="fas {{ $icon }}" aria-hidden="true"></i>
                  </div>
                  <span class="process-timeline-label">Étape {{ $loop->iteration }}</span>
                </div>
                <h3>{{ $stepTitle }}</h3>
                @if(filled($step['description'] ?? null))
                  <p class="process-timeline-summary">{{ $step['description'] }}</p>
                @endif
                @if($hasDetailHtml)
                  <button type="button"
                          class="process-timeline-read-more"
                          data-process-step-detail
                          data-step-index="{{ $loop->iteration }}"
                          data-step-title="{{ $stepTitle !== '' ? $stepTitle : 'Étape ' . $loop->iteration }}"
                          aria-haspopup="dialog">
                    <span>Lire l'explication détaillée</span>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                  </button>
                  <div class="process-timeline-detail-source" hidden data-process-step-detail-html>
                    {!! $step['detail_html'] !!}
                  </div>
                @endif
              </div>
            </article>
          @endforeach
        </div>
      </div>

      <div id="processStepDetailModal"
           class="service-section-modal"
           hidden
           aria-hidden="true"
           role="dialog"
           aria-modal="true"
           aria-labelledby="processStepDetailModalTitle">
        <div class="service-section-modal__backdrop" data-process-step-detail-close></div>
        <div class="service-section-modal__shell">
          <header class="service-section-modal__header">
            <div class="service-section-modal__meta">
              <span class="service-section-modal__badge">Étape</span>
              <span id="processStepDetailModalIndex" class="service-section-modal__index"></span>
            </div>
            <button type="button"
                    class="service-section-modal__close"
                    data-process-step-detail-close
                    aria-label="Fermer">
              <i class="fas fa-times" aria-hidden="true"></i>
            </button>
          </header>

          <div class="service-section-modal__body">
            <h2 id="processStepDetailModalTitle" class="service-section-modal__title"></h2>
            <div id="processStepDetailModalContent" class="service-section-modal__content prose-vitrine process-step-detail-modal-content"></div>
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
  (function initProcessStepDetailModal() {
    const modal = document.getElementById('processStepDetailModal');
    if (!modal) return;

    if (modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }

    const titleEl = document.getElementById('processStepDetailModalTitle');
    const indexEl = document.getElementById('processStepDetailModalIndex');
    const contentEl = document.getElementById('processStepDetailModalContent');
    const closeEls = modal.querySelectorAll('[data-process-step-detail-close]');
    let lastFocused = null;

    function lockBody(lock) {
      document.body.classList.toggle('service-section-modal-open', lock);
    }

    function openModal(button) {
      lastFocused = document.activeElement;

      const title = button.dataset.stepTitle || 'Étape';
      const index = button.dataset.stepIndex || '';
      const source = button.parentElement?.querySelector('[data-process-step-detail-html]');

      if (titleEl) titleEl.textContent = title;
      if (indexEl) {
        indexEl.textContent = index !== '' ? String(index).padStart(2, '0') : '';
        indexEl.hidden = index === '';
      }

      if (contentEl) {
        contentEl.innerHTML = source ? source.innerHTML : '';
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
      const trigger = event.target.closest('[data-process-step-detail]');
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
