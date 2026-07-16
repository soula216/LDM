@props([
    'links' => [],
    'modifier' => '',
    'flagSrc' => null,
    'flagAlt' => 'Drapeau de la Tunisie',
])

@php
    $links = collect($links)
        ->filter(function ($social) {
            $url = trim((string) ($social['url'] ?? ''));

            return $url !== '' && $url !== '#';
        })
        ->values()
        ->all();
@endphp

@if(count($links) > 0 || filled($flagSrc))
<div @class(['social-links', $modifier])>
  @if(filled($flagSrc))
    <span class="footer-flag" aria-hidden="true">
      <img src="{{ $flagSrc }}" alt="{{ $flagAlt }}">
    </span>
  @endif
  @foreach($links as $social)
    @php
      $socialLabel = $social['label'] ?? 'Réseau social';
      $iconClass = trim((string) ($social['icon'] ?? ''));
      $socialUrl = trim((string) ($social['url'] ?? ''));

      if ($iconClass === '') {
          $labelKey = strtolower($socialLabel);
          $iconClass = match (true) {
              str_contains($labelKey, 'facebook') => 'fab fa-facebook-f',
              str_contains($labelKey, 'instagram') => 'fab fa-instagram',
              str_contains($labelKey, 'tiktok') => 'fab fa-tiktok',
              str_contains($labelKey, 'linkedin') => 'fab fa-linkedin-in',
              str_contains($labelKey, 'twitter') || str_contains($labelKey, 'x.com') => 'fab fa-x-twitter',
              str_contains($labelKey, 'youtube') => 'fab fa-youtube',
              default => 'fas fa-share-alt',
          };
      }
    @endphp
    <a href="{{ $socialUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLabel }}">
      <i class="{{ $iconClass }}" aria-hidden="true"></i>
    </a>
  @endforeach
</div>
@endif
