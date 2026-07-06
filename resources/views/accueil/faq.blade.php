@extends('layouts.accueil')

@section('title', ($faq['section_title'] ?? 'Foire Aux Questions') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    use App\Models\VitrineBlock;

    $items = VitrineBlock::activeFaqItems($faq['items'] ?? [])
        ->filter(fn ($item) => filled($item['question'] ?? null));
@endphp

<main class="faq-page">
  <section class="faq-hero">
    <div class="faq-hero-bg" aria-hidden="true"></div>
    <div class="faq-hero-content">
      <div class="faq-hero-badge">
        <i class="fas fa-circle-question" aria-hidden="true"></i>
        <span>{{ $faq['section_label'] ?? 'FAQ' }}</span>
      </div>
      <h1>{{ $faq['section_title'] ?? 'Foire Aux Questions' }}</h1>
      @if(filled($faq['section_subtitle'] ?? null))
        <p>{{ $faq['section_subtitle'] }}</p>
      @endif
      @if($items->isNotEmpty())
        <div class="faq-hero-meta">
          <div class="faq-hero-stat">
            <strong>{{ $items->count() }}</strong>
            <span>question{{ $items->count() > 1 ? 's' : '' }}</span>
          </div>
          <div class="faq-hero-stat">
            <strong>24h</strong>
            <span>réponse moyenne</span>
          </div>
        </div>
      @endif
    </div>
  </section>

  <section class="faq-body" id="faq">
    @if($items->isEmpty())
      <div class="faq-empty">
        <div class="faq-empty-icon">
          <i class="fas fa-comments" aria-hidden="true"></i>
        </div>
        <h2>Questions à venir</h2>
        <p>Les réponses aux questions fréquentes seront bientôt disponibles ici.</p>
      </div>
    @else
      <div class="faq-body-inner">
        <div class="faq-intro">
          <p>
            <i class="fas fa-hand-pointer" aria-hidden="true"></i>
            Cliquez sur une question pour afficher la réponse détaillée de notre équipe.
          </p>
        </div>

        <div class="faq-accordion" data-faq-accordion>
          @foreach($items as $index => $item)
            <article class="faq-card" data-faq-item>
              <button type="button"
                      class="faq-card__trigger"
                      data-faq-trigger
                      aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                      aria-controls="faq-panel-{{ $index }}">
                <span class="faq-card__index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <span class="faq-card__question">{{ $item['question'] }}</span>
                <span class="faq-card__toggle" aria-hidden="true">
                  <i class="fas fa-plus faq-card__icon faq-card__icon--plus"></i>
                  <i class="fas fa-minus faq-card__icon faq-card__icon--minus"></i>
                </span>
              </button>
              <div class="faq-card__panel"
                   id="faq-panel-{{ $index }}"
                   data-faq-panel
                   @if($index !== 0) hidden @endif>
                <div class="faq-card__answer">
                  <p>{{ $item['answer'] ?? '' }}</p>
                </div>
              </div>
            </article>
          @endforeach
        </div>

        <aside class="faq-cta reveal">
          <div class="faq-cta__content">
            <h2>Vous ne trouvez pas votre réponse ?</h2>
            <p>Notre équipe est disponible pour répondre à toutes vos questions techniques et commerciales.</p>
          </div>
          <a href="{{ route('vitrine') }}#contact" class="faq-cta__btn">
            <span>Nous contacter</span>
            <i class="fas fa-arrow-right" aria-hidden="true"></i>
          </a>
        </aside>
      </div>
    @endif
  </section>
</main>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection
