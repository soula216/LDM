@props([
    'section',
    'title',
    'subtitle' => '',
    'headerClass' => 'border-b border-border/60 bg-card/80',
])

<button
    type="button"
    @click="open.{{ $section }} = !open.{{ $section }}"
    class="w-full px-4 sm:px-6 py-4 sm:py-5 {{ $headerClass }} flex items-center justify-between gap-4 text-left group transition-colors hover:bg-neutral-50/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-primary/25"
    :aria-expanded="open.{{ $section }}"
>
    <div class="flex items-center gap-3 min-w-0 flex-1">
        @if (isset($icon))
            {{ $icon }}
        @endif
        <div class="min-w-0">
            <h4 class="text-sm sm:text-base font-bold text-primary">{{ $title }}</h4>
            @if ($subtitle)
                <p class="text-xs text-secondary mt-0.5 truncate">{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-2 shrink-0">
        @if (isset($actions))
            <div class="flex items-center gap-2" @click.stop>
                {{ $actions }}
            </div>
        @endif
        <span
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-border/70 bg-card text-secondary shadow-sm transition-all duration-200 group-hover:border-primary/30 group-hover:text-primary"
            :class="open.{{ $section }} ? 'rotate-180 bg-primary/5 border-primary/20 text-primary' : ''"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
            </svg>
        </span>
    </div>
</button>
