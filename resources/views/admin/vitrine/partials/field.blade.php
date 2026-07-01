{{-- Champs réutilisables --}}
@props(['label', 'name', 'value' => '', 'type' => 'text', 'placeholder' => '', 'rows' => 3, 'hint' => null])

<div class="vitrine-field w-full min-w-0">
    <label for="{{ $name }}" class="block text-xs sm:text-sm font-semibold text-primary mb-1.5">{{ $label }}</label>
    @if($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
                  class="input-field resize-y min-h-[80px]">{{ old(str_replace(['content[', ']'], ['content.', ''], $name), $value) }}</textarea>
    @else
        <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old(str_replace(['content[', ']'], ['content.', ''], $name), $value) }}"
               placeholder="{{ $placeholder }}" class="input-field">
    @endif
    @if($hint)
        <p class="mt-1 text-xs text-secondary">{{ $hint }}</p>
    @endif
</div>
