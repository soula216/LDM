@php
    $tabIcons = [
        'header' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
        'hero' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        'services' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        'process' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        'gallery' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        'features' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
        'about' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'laboratory' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'partners' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'faq' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'recrutement' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'academy' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        'contact' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'footer' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    ];
@endphp

@include('admin.vitrine.partials.service-content-html-editor-assets')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/30 to-accent/30 rounded-2xl blur-lg"></div>
                    <div class="relative p-3 bg-gradient-to-br from-primary to-accent rounded-2xl shadow-lg shadow-primary/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-primary tracking-tight">
                        {{ __('Site vitrine') }}
                    </h2>
                    <p class="text-sm text-secondary mt-0.5">Gérez le contenu de chaque section du site public</p>
                </div>
            </div>
            <a href="{{ route('vitrine') }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-border bg-card/80 backdrop-blur text-sm font-medium text-primary hover:bg-neutral-50 hover:border-primary/30 transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Prévisualiser le site
            </a>
        </div>
    </x-slot>

    <div x-data="{ activeTab: @js($activeTab) }"
         x-init="
            $watch('activeTab', (tab) => {
                if (tab === 'services') $dispatch('vitrine-services-tab-open');
                if (tab === 'process') $dispatch('vitrine-process-tab-open');
                if (tab === 'about') $dispatch('vitrine-about-tab-open');
                if (tab === 'recrutement') $dispatch('vitrine-recrutement-tab-open');
            });
            if (activeTab === 'services') { $nextTick(() => $dispatch('vitrine-services-tab-open')); }
            if (activeTab === 'process') { $nextTick(() => $dispatch('vitrine-process-tab-open')); }
            if (activeTab === 'about') { $nextTick(() => $dispatch('vitrine-about-tab-open')); }
            if (activeTab === 'recrutement') { $nextTick(() => $dispatch('vitrine-recrutement-tab-open')); }
         "
         class="py-6 sm:py-10 bg-app min-h-screen">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl flex items-center gap-3 shadow-sm">
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-danger/10 border border-danger/20 rounded-2xl">
                    <p class="text-danger font-medium text-sm mb-2">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc list-inside text-danger text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Menu gauche + contenu droite (côte à côte) --}}
            <div class="vitrine-admin-layout flex flex-row gap-6 lg:gap-8 items-start">
                <aside class="vitrine-admin-nav w-56 xl:w-64 shrink-0 sticky top-24 self-start">
                    <nav class="bg-card/60 backdrop-blur-md border border-border rounded-2xl shadow-sm overflow-hidden"
                         aria-label="Sections du site vitrine">
                        <div class="px-4 py-3 border-b border-border/70">
                            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-secondary">Sections</p>
                        </div>
                        <div class="p-1.5 space-y-1">
                            @foreach($blocks as $block)
                                @continue($block->key === 'laboratory')
                                <button
                                    type="button"
                                    @click="activeTab = '{{ $block->key }}'"
                                    :class="activeTab === '{{ $block->key }}'
                                        ? 'bg-gradient-to-r from-primary to-primary/90 text-white shadow-md shadow-primary/25'
                                        : 'text-secondary hover:text-primary hover:bg-neutral-100/80'"
                                    class="w-full flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 text-left"
                                >
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tabIcons[$block->key] ?? 'M4 6h16M4 12h16M4 18h16' }}"></path>
                                    </svg>
                                    <span class="truncate">{{ $block->label }}</span>
                                </button>
                            @endforeach
                        </div>
                    </nav>
                </aside>

                <div class="vitrine-admin-content min-w-0 flex-1">
                    @foreach($blocks as $block)
                        @continue($block->key === 'laboratory')
                        <div class="{{ $activeTab === $block->key ? '' : 'hidden' }}"
                             :class="{ 'hidden': activeTab !== '{{ $block->key }}' }">
                            <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden">
                                <div class="px-6 py-5 border-b border-border bg-gradient-to-r from-neutral-50/80 to-card">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-primary">{{ $block->label }}</h3>
                                            <p class="text-sm text-secondary mt-0.5">Modifiez le contenu affiché sur le site vitrine</p>
                                        </div>
                                        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                                            Bloc actif
                                        </span>
                                    </div>
                                </div>

                                @if($block->key === 'about')
                                    <div class="p-4 sm:p-6">
                                        @include('admin.vitrine.partials.forms.about', [
                                            'content' => $block->content,
                                            'aboutBlock' => $block,
                                            'laboratoryBlock' => $blocks->firstWhere('key', 'laboratory'),
                                            'activeAboutSub' => $activeAboutSub ?? 'qui-sommes-nous',
                                        ])
                                    </div>
                                @else
                                    <form action="{{ route('admin.vitrine.update', $block) }}" method="POST" class="p-4 sm:p-6" @if(in_array($block->key, ['hero', 'gallery', 'partners', 'services', 'academy', 'header', 'footer'])) enctype="multipart/form-data" @endif @if($block->key === 'academy') x-data="{ submitting: false }" @submit="submitting = true" @endif>
                                        @csrf
                                        @method('PATCH')

                                        @include('admin.vitrine.partials.forms.' . $block->key, ['content' => $block->content])

                                        <div class="mt-8 pt-6 border-t border-border flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                                            <label class="inline-flex items-center gap-3 cursor-pointer group">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" name="is_active" value="1" {{ $block->is_active ? 'checked' : '' }}
                                                       class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                                                <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher ce bloc sur le site</span>
                                            </label>
                                            @if($block->key === 'academy')
                                                <button type="submit"
                                                        :class="submitting ? 'opacity-85 cursor-progress pointer-events-none' : ''"
                                                        :aria-busy="submitting ? 'true' : 'false'"
                                                        class="btn-primary inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/25 transition-all">
                                                    <svg x-show="submitting" x-cloak class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <svg x-show="!submitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <span x-text="submitting ? 'Enregistrer en cours' : 'Enregistrer {{ $block->label }}'"></span>
                                                </button>
                                            @else
                                                <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/25 transition-all">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Enregistrer {{ $block->label }}
                                                </button>
                                            @endif
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <style>
                .vitrine-admin-layout {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: flex-start;
                    gap: 1.5rem;
                }
                .vitrine-admin-nav {
                    width: 14rem;
                    flex-shrink: 0;
                    position: sticky;
                    top: 6rem;
                }
                .vitrine-admin-content {
                    flex: 1 1 0%;
                    min-width: 0;
                }
                @media (max-width: 640px) {
                    .vitrine-admin-layout {
                        flex-direction: column !important;
                    }
                    .vitrine-admin-nav {
                        width: 100%;
                        position: static;
                    }
                }
            </style>
        </div>
    </div>
</x-app-layout>
