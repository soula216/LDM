@php
    use App\Models\VitrineBlock;

    $imageUrl = VitrineBlock::serviceItemImageUrl($item);
    $slug = VitrineBlock::serviceItemSlug($item);
    $title = mb_strtoupper($item['title'] ?? '', 'UTF-8');
    $labelClass = mb_strlen($title) > 22 || substr_count($title, ' ') >= 2 ? ' is-compact' : '';
@endphp
<article class="services-hex-item reveal active"
         data-grid-row="{{ $gridRow }}"
         data-grid-col="{{ $gridCol }}"
         data-role="{{ $role }}"
         style="grid-row: {{ $gridRow }}; grid-column: {{ $gridCol }};">
  <a href="{{ route('vitrine.services.show', $slug) }}" class="services-hex-link" aria-label="{{ $item['title'] ?? 'Service' }}">
    <div class="services-hex-shell-wrap">
      <div class="services-hex-shell{{ $imageUrl !== '' ? ' has-image' : ' is-placeholder' }}"
           @if($imageUrl !== '') style="background-image: url('{{ e($imageUrl) }}')" @endif>
        @if($imageUrl === '')
          <div class="services-hex-placeholder" aria-hidden="true">
            <i class="fas fa-tooth"></i>
          </div>
        @endif
        <div class="services-hex-overlay" aria-hidden="true"></div>
      </div>
      <h2 class="services-hex-label{{ $labelClass }}">{{ $title }}</h2>
    </div>
  </a>
</article>
