@props([
    'links' => [],
    'modifier' => '',
    'iconSize' => 40,
])

@if(count($links) > 0)
<div @class(['social-links', $modifier])>
  @foreach($links as $social)
    @php
      $iconSourceType = $social['icon_source_type'] ?? (filled($social['icon_url'] ?? null) ? 'url' : 'fontawesome');
      $socialLabel = $social['label'] ?? 'Réseau social';
    @endphp
    <a href="{{ $social['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLabel }}">
      @if($iconSourceType !== 'fontawesome' && filled($social['icon_url'] ?? null))
        <img src="{{ $social['icon_url'] }}" alt="" class="social-icon-img" width="{{ $iconSize }}" height="{{ $iconSize }}">
      @elseif(filled($social['icon'] ?? null))
        <i class="{{ $social['icon'] }}" aria-hidden="true"></i>
      @endif
    </a>
  @endforeach
</div>
@endif
