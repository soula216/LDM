@extends('layouts.accueil')

@section('title', ($recrutement['section_title'] ?? 'Recrutement') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;

    $items = VitrineBlock::activeRecruitmentItems($recrutement['items'] ?? []);
    $totalVacancies = $items->sum(fn ($item) => (int) ($item['vacancies'] ?? 0));
@endphp

<main class="recrutement-page">
  <section class="recrutement-hero">
    <div class="recrutement-hero__bg" aria-hidden="true"></div>
    <div class="recrutement-hero__mesh" aria-hidden="true"></div>
    <div class="recrutement-hero__content">
      <div class="recrutement-hero__badge">
        <i class="fas fa-briefcase" aria-hidden="true"></i>
        <span>{{ $recrutement['section_label'] ?? 'Recrutement' }}</span>
      </div>
      <h1>{{ $recrutement['section_title'] ?? 'Rejoindre LDM' }}</h1>
      @if(filled($recrutement['section_subtitle'] ?? null))
        <p class="recrutement-hero__lead">{{ $recrutement['section_subtitle'] }}</p>
      @endif
      @if($items->isNotEmpty())
        <div class="recrutement-hero__stats">
          <div class="recrutement-hero__stat">
            <strong>{{ $items->count() }}</strong>
            <span>offre{{ $items->count() > 1 ? 's' : '' }}</span>
          </div>
          @if($totalVacancies > 0)
            <div class="recrutement-hero__stat">
              <strong>{{ $totalVacancies }}</strong>
              <span>poste{{ $totalVacancies > 1 ? 's' : '' }} ouvert{{ $totalVacancies > 1 ? 's' : '' }}</span>
            </div>
          @endif
        </div>
      @endif
    </div>
  </section>

  <section class="recrutement-body">
    @if($items->isEmpty())
      <div class="recrutement-empty reveal">
        <div class="recrutement-empty__icon"><i class="fas fa-briefcase" aria-hidden="true"></i></div>
        <h2>Aucune offre pour le moment</h2>
        <p>Les postes ouverts seront bientôt publiés ici. Revenez régulièrement ou contactez-nous spontanément.</p>
      </div>
    @else
      <div class="recrutement-grid">
        @foreach($items as $index => $item)
          <article class="recrutement-card reveal" style="--rec-delay: {{ min($index * 0.06, 0.4) }}s">
            <header class="recrutement-card__head">
              <div class="recrutement-card__titles">
                <h2>{{ $item['title'] }}</h2>
                <div class="recrutement-card__cta">
                  @if(($item['vacancies'] ?? 0) > 0)
                    <span class="recrutement-card__vacancies">
                      <i class="fas fa-user-plus" aria-hidden="true"></i>
                      {{ $item['vacancies'] }} poste{{ $item['vacancies'] > 1 ? 's' : '' }} ouvert{{ $item['vacancies'] > 1 ? 's' : '' }}
                    </span>
                  @endif
                  <button
                    type="button"
                    class="recrutement-apply-btn"
                    data-job-apply
                    data-job-title="{{ $item['title'] }}"
                  >
                    Postuler maintenant
                  </button>
                </div>
              </div>
              @if(! empty($item['employment_types']))
                <div class="recrutement-card__types">
                  @foreach($item['employment_types'] as $type)
                    <span>{{ $type }}</span>
                  @endforeach
                </div>
              @endif
            </header>

            <dl class="recrutement-card__meta">
              @if(filled($item['experience'] ?? null))
                <div>
                  <dt><i class="fas fa-clock" aria-hidden="true"></i> Expérience</dt>
                  <dd>{{ $item['experience'] }}</dd>
                </div>
              @endif
              @if(filled($item['education_level'] ?? null))
                <div>
                  <dt><i class="fas fa-graduation-cap" aria-hidden="true"></i> Niveau d’étude</dt>
                  <dd>{{ $item['education_level'] }}</dd>
                </div>
              @endif
              @if(filled($item['languages'] ?? null))
                <div>
                  <dt><i class="fas fa-language" aria-hidden="true"></i> Langue</dt>
                  <dd>{{ $item['languages'] }}</dd>
                </div>
              @endif
              @if(filled($item['gender'] ?? null))
                <div>
                  <dt><i class="fas fa-users" aria-hidden="true"></i> Genre</dt>
                  <dd>{{ $item['gender'] }}</dd>
                </div>
              @endif
            </dl>

            @if(filled($item['description_html'] ?? null))
              <div class="recrutement-card__description">
                <h3 class="recrutement-card__description-label">Description de l’emploi</h3>
                <div class="prose-vitrine">
                  {!! $item['description_html'] !!}
                </div>
              </div>
            @endif
          </article>
        @endforeach
      </div>
    @endif
  </section>

  @if($items->isNotEmpty())
    @php
      $openApplyOnLoad = $errors->any() || old('job_title') || session('job_application_job_title');
      $prefillJobTitle = old('job_title', session('job_application_job_title', ''));
    @endphp

    @if(session('job_application_success'))
      <div class="recrutement-flash recrutement-flash--success" role="status" data-recrutement-flash>
        <button type="button" class="recrutement-flash__close" data-recrutement-flash-close aria-label="Fermer">
          <i class="fas fa-times" aria-hidden="true"></i>
        </button>
        <div class="recrutement-flash__body">
          <i class="fas fa-check-circle" aria-hidden="true"></i>
          <span>{{ session('job_application_success') }}</span>
        </div>
      </div>
    @endif

    <div
      id="job-apply-modal"
      class="job-apply-modal"
      hidden
      role="dialog"
      aria-modal="true"
      aria-labelledby="job-apply-modal-title"
      data-open-on-load="{{ $openApplyOnLoad ? '1' : '0' }}"
    >
      <div class="job-apply-modal__backdrop" data-job-apply-close tabindex="-1"></div>
      <div class="job-apply-modal__shell">
        <button type="button" class="job-apply-modal__close" data-job-apply-close aria-label="Fermer">
          <i class="fas fa-times" aria-hidden="true"></i>
        </button>

        <h2 id="job-apply-modal-title" class="job-apply-modal__title">
          Postuler à <span data-job-apply-title>{{ $prefillJobTitle ?: 'cette offre' }}</span>
        </h2>

        @if($errors->any())
          <div class="job-apply-modal__errors" role="alert">
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form
          class="job-apply-form"
          action="{{ route('job-applications.store') }}"
          method="post"
          enctype="multipart/form-data"
          novalidate
        >
          @csrf
          <input type="hidden" name="job_title" id="job-apply-job-title" value="{{ $prefillJobTitle }}">

          <div class="job-apply-field">
            <label for="job-apply-name">Votre nom</label>
            <input
              type="text"
              id="job-apply-name"
              name="name"
              value="{{ old('name') }}"
              required
              autocomplete="name"
            >
          </div>

          <div class="job-apply-field">
            <label for="job-apply-email">Votre e-mail</label>
            <input
              type="email"
              id="job-apply-email"
              name="email"
              value="{{ old('email') }}"
              required
              autocomplete="email"
            >
          </div>

          <div class="job-apply-field">
            <label for="job-apply-phone">Numéro de téléphone</label>
            <input
              type="tel"
              id="job-apply-phone"
              name="phone"
              value="{{ old('phone') }}"
              required
              autocomplete="tel"
            >
          </div>

          <div class="job-apply-field">
            <label for="job-apply-cv">Téléversez votre CV</label>
            <input
              type="file"
              id="job-apply-cv"
              name="cv"
              accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
              required
            >
            <p class="job-apply-field__hint">PDF ou Word (DOC, DOCX) — max. 10 Mo</p>
          </div>

          <div class="job-apply-field">
            <label for="job-apply-cover">Lettre de motivation</label>
            <textarea
              id="job-apply-cover"
              name="cover_letter"
              rows="4"
              placeholder="Présentez-vous en quelques lignes…"
            >{{ old('cover_letter') }}</textarea>
          </div>

          <button type="submit" class="job-apply-submit">Envoyer la candidature</button>
        </form>
      </div>
    </div>
  @endif
</main>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection

@push('scripts')
<script>
(function () {
  const flash = document.querySelector('[data-recrutement-flash]');
  if (flash) {
    const closeFlash = () => flash.remove();
    const closeBtn = flash.querySelector('[data-recrutement-flash-close]');
    if (closeBtn) closeBtn.addEventListener('click', closeFlash);
  }

  const modal = document.getElementById('job-apply-modal');
  if (!modal) return;

  const titleSpan = modal.querySelector('[data-job-apply-title]');
  const jobTitleInput = document.getElementById('job-apply-job-title');
  const openButtons = document.querySelectorAll('[data-job-apply]');
  const closeTriggers = modal.querySelectorAll('[data-job-apply-close]');

  function openModal(jobTitle) {
    const title = (jobTitle || '').trim() || 'cette offre';
    if (titleSpan) titleSpan.textContent = title;
    if (jobTitleInput) jobTitleInput.value = title === 'cette offre' ? '' : title;
    modal.hidden = false;
    document.body.classList.add('job-apply-modal-open');
    const firstField = modal.querySelector('#job-apply-name');
    if (firstField) firstField.focus();
  }

  function closeModal() {
    modal.hidden = true;
    document.body.classList.remove('job-apply-modal-open');
  }

  openButtons.forEach((btn) => {
    btn.addEventListener('click', () => openModal(btn.getAttribute('data-job-title') || ''));
  });

  closeTriggers.forEach((el) => {
    el.addEventListener('click', closeModal);
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.hidden) closeModal();
  });

  if (modal.getAttribute('data-open-on-load') === '1') {
    openModal(jobTitleInput ? jobTitleInput.value : '');
  }
})();
</script>
@endpush
