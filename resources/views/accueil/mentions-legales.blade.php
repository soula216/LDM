@extends('layouts.accueil')

@section('title', ($mentionsLegales['section_title'] ?? 'Mentions légales') . ' | LDM - Digital Max')

@section('header')
  @include('accueil.header')
@endsection

@section('content')
@php
    $title = trim((string) ($mentionsLegales['section_title'] ?? 'Mentions légales'));
    $label = trim((string) ($mentionsLegales['section_label'] ?? 'Mentions légales'));
    $contentHtml = trim((string) ($mentionsLegales['content_html'] ?? ''));
@endphp

<main class="legal-page">
  <section class="legal-hero">
    <div class="legal-hero__bg" aria-hidden="true"></div>
    <div class="legal-hero__content">
      @if($label !== '')
        <div class="legal-hero__badge">
          <i class="fas fa-scale-balanced" aria-hidden="true"></i>
          <span>{{ $label }}</span>
        </div>
      @endif
      <h1>{{ $title !== '' ? $title : 'Mentions légales' }}</h1>
    </div>
  </section>

  <section class="legal-body">
    @if($contentHtml !== '')
      <article class="legal-article reveal">
        <div class="legal-article__card">
          <div class="legal-article__content prose-vitrine">
            {!! $contentHtml !!}
          </div>
        </div>
      </article>
    @else
      <div class="legal-empty reveal">
        <div class="legal-empty__icon"><i class="fas fa-file-lines" aria-hidden="true"></i></div>
        <h2>Contenu à venir</h2>
        <p>Les mentions légales seront bientôt disponibles sur cette page.</p>
      </div>
    @endif
  </section>
</main>
@endsection

@section('footer')
  @include('accueil.footer')
@endsection
