@php
    $c = $content;
    $heroSlides = collect($c['slides'] ?? [])->map(function ($slide) {
        $url = \App\Models\VitrineBlock::resolveImageAbsoluteUrl($slide['image_url'] ?? '');
        $sourceType = $slide['source_type'] ?? null;
        if (! $sourceType) {
            $sourceType = str_contains($url, '/storage/vitrine/slider') || str_contains($slide['image_url'] ?? '', '/storage/vitrine/slider')
                ? 'upload'
                : 'url';
        }

        return [
            'image_url' => $url,
            'source_type' => $sourceType,
            'preview_url' => null,
        ];
    })->values()->all();
@endphp

<div class="vitrine-hero-form space-y-6 sm:space-y-8"
     x-data="{
        open: { slider: true, text: true, buttons: true, card: true },
        slides: @js($heroSlides),
        buttons: @js($c['buttons'] ?? []),
        stats: @js($c['card']['stats'] ?? []),
        addSlide() {
            this.slides.push({ image_url: '', source_type: 'url', preview_url: null });
        },
        slidePreview(slide) {
            return slide.preview_url || slide.image_url || '';
        },
        onSlideFileChange(event, slide) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (slide.preview_url?.startsWith('blob:')) {
                URL.revokeObjectURL(slide.preview_url);
            }
            slide.preview_url = URL.createObjectURL(file);
        },
        removeSlide(index) {
            const slide = this.slides[index];
            if (slide?.preview_url?.startsWith('blob:')) {
                URL.revokeObjectURL(slide.preview_url);
            }
            this.slides.splice(index, 1);
        }
     }">

    {{-- ═══ SECTION 1 : Slider ═══ --}}
    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'slider',
            'title' => 'Images du slider',
            'subtitle' => 'Arrière-plan défilant de la bannière principale',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-indigo-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.slider" x-cloak x-transition.opacity.duration.200ms>
        <div class="p-4 sm:p-6">
            <template x-if="slides.length === 0">
                <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50">
                    <svg class="w-10 h-10 mx-auto text-secondary/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm text-secondary font-medium">Aucune image — ajoutez au moins une diapositive</p>
                </div>
            </template>

            <div class="space-y-4" x-show="slides.length > 0">
                <template x-for="(slide, index) in slides" :key="'slide-' + index">
                    <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 text-xs font-bold"
                                  x-text="index + 1"></span>
                            <button type="button" @click="removeSlide(index)"
                                    class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                    title="Supprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="px-4 py-3 sm:px-5 sm:py-4 border-b border-border/60">
                            <div class="min-w-0 w-full">
                                    <input type="hidden" :name="'content[slides][' + index + '][source_type]'" x-model="slide.source_type">

                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <button type="button"
                                                @click="slide.source_type = 'url'"
                                                :class="slide.source_type === 'url' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            URL
                                        </button>
                                        <button type="button"
                                                @click="slide.source_type = 'upload'"
                                                :class="slide.source_type === 'upload' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                            Upload
                                        </button>
                                    </div>

                                    {{-- Mode URL --}}
                                    <template x-if="slide.source_type === 'url'">
                                        <div>
                                            <label :for="'slide-url-' + index" class="flex items-center gap-2 mb-2.5">
                                                <span class="text-xs font-semibold text-secondary tracking-wide">URL de l'image</span>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-danger/10 text-danger">Requis</span>
                                            </label>

                                            <div class="vitrine-slide-url-field group/url relative flex items-center gap-2 rounded-xl border border-border/70 bg-gradient-to-b from-card to-neutral-50/40 shadow-sm transition-all duration-300 hover:border-primary/30 hover:shadow-md focus-within:border-primary/50 focus-within:ring-4 focus-within:ring-primary/10 focus-within:shadow-lg focus-within:shadow-primary/5">
                                                <div class="flex items-center justify-center shrink-0 w-11 h-11 m-1.5 rounded-lg bg-gradient-to-br from-primary to-primary/80 text-white shadow-sm shadow-primary/25">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                    </svg>
                                                </div>

                                                <input
                                                    type="text"
                                                    inputmode="url"
                                                    :id="'slide-url-' + index"
                                                    :name="'content[slides][' + index + '][image_url]'"
                                                    x-model="slide.image_url"
                                                    placeholder="https://exemple.com/mon-image.jpg"
                                                    autocomplete="off"
                                                    spellcheck="false"
                                                    class="vitrine-slide-url-input flex-1 min-w-0 bg-transparent py-3.5 pl-1 pr-4 text-sm text-primary placeholder:text-secondary/45 font-mono tracking-tight"
                                                >

                                                <div class="shrink-0 pr-3" x-show="slide.image_url && slide.image_url.trim() !== ''" x-cloak>
                                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/20" title="URL renseignée">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>

                                            <p class="mt-2.5 flex items-start gap-2 text-xs text-secondary/80 leading-relaxed">
                                                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 text-secondary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Collez l'adresse complète de l'image hébergée en ligne (JPEG, PNG, WebP ou GIF).</span>
                                            </p>
                                        </div>
                                    </template>

                                    {{-- Mode Upload --}}
                                    <template x-if="slide.source_type === 'upload'">
                                        <div>
                                            <label class="block text-xs font-bold text-primary uppercase tracking-wide mb-2">
                                                Image depuis votre ordinateur
                                            </label>
                                            <input type="hidden" :name="'content[slides][' + index + '][image_url]'" :value="slide.image_url">
                                            <label class="flex flex-col items-center justify-center w-full min-h-[120px] px-4 py-6 border-2 border-dashed border-border rounded-xl bg-card hover:border-primary/40 hover:bg-primary/5 transition-all cursor-pointer">
                                                <input type="file"
                                                       :name="'slide_uploads[' + index + ']'"
                                                       accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                                                       class="sr-only"
                                                       @change="onSlideFileChange($event, slide)">
                                                <svg class="w-8 h-8 text-secondary/50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="text-sm font-medium text-primary">Cliquez pour choisir une image</span>
                                                <span class="text-xs text-secondary mt-1">JPEG, PNG, WebP, GIF — max 5 Mo</span>
                                            </label>
                                            <p class="mt-2 text-xs text-secondary break-all" x-show="slide.image_url && !slide.preview_url">
                                                Fichier actuel : <span x-text="slide.image_url"></span>
                                            </p>
                                            <p class="mt-2 text-xs text-emerald-600 font-medium" x-show="slide.preview_url">
                                                Nouvelle image sélectionnée — enregistrez pour appliquer
                                            </p>
                                        </div>
                                    </template>
                            </div>
                        </div>

                        {{-- Aperçu --}}
                        <div class="p-4 sm:p-5">
                            <p class="text-xs font-semibold text-secondary uppercase tracking-wide mb-2">Aperçu</p>
                            <div class="aspect-[16/7] sm:aspect-[16/6] max-h-48 sm:max-h-56 bg-neutral-100 rounded-lg relative overflow-hidden border border-border/40">
                                <template x-if="slidePreview(slide)">
                                    <img :src="slidePreview(slide)" alt="" class="w-full h-full object-cover" @@error="$el.style.display='none'">
                                </template>
                                <template x-if="!slidePreview(slide)">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-secondary/50 gap-2">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-xs font-medium text-center px-4" x-text="slide.source_type === 'upload' ? 'Choisissez une image à uploader' : 'Saisissez une URL pour voir l\'aperçu'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-4 pt-4 border-t border-border/60">
                <button type="button" @click="addSlide()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-primary hover:bg-primary/90 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ajouter une image
                </button>
            </div>
        </div>
        </div>
    </section>

    {{-- ═══ SECTION 2 : Contenu principal ═══ --}}
    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'text',
            'title' => 'Texte principal',
            'subtitle' => 'Badge, titre et description affichés sur la bannière',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-primary/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.text" x-cloak x-transition.opacity.duration.200ms>
        <div class="p-4 sm:p-6 space-y-6">
            {{-- Badge --}}
            <div class="p-4 sm:p-5 rounded-xl bg-neutral-50/80 border border-border/60">
                <p class="text-xs font-bold text-primary uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    Badge de certification
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    @include('admin.vitrine.partials.field', [
                        'label' => 'Icône (Font Awesome)',
                        'name' => 'content[badge_icon]',
                        'value' => $c['badge_icon'] ?? '',
                        'placeholder' => 'fas fa-certificate',
                        'hint' => 'Ex : fas fa-certificate',
                    ])
                    @include('admin.vitrine.partials.field', [
                        'label' => 'Texte du badge',
                        'name' => 'content[badge_text]',
                        'value' => $c['badge_text'] ?? '',
                        'placeholder' => 'Laboratoire Certifié ISO 13485',
                    ])
                </div>
            </div>

            {{-- Titres --}}
            <div class="p-4 sm:p-5 rounded-xl bg-neutral-50/80 border border-border/60">
                <p class="text-xs font-bold text-primary uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    Titre principal
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    @include('admin.vitrine.partials.field', [
                        'label' => 'Ligne 1',
                        'name' => 'content[title_line1]',
                        'value' => $c['title_line1'] ?? '',
                        'placeholder' => 'Prothèses Dentaires',
                    ])
                    @include('admin.vitrine.partials.field', [
                        'label' => 'Mise en avant (surbrillance)',
                        'name' => 'content[title_highlight]',
                        'value' => $c['title_highlight'] ?? '',
                        'placeholder' => 'de Précision',
                    ])
                </div>
                <div class="mt-4 p-3 sm:p-4 rounded-lg bg-card border border-border/40">
                    <p class="text-xs text-secondary mb-1">Aperçu du titre</p>
                    <p class="text-lg sm:text-xl font-bold text-primary leading-tight">
                        {{ $c['title_line1'] ?? 'Prothèses Dentaires' }}
                        <span class="text-primary/70">{{ $c['title_highlight'] ?? 'de Précision' }}</span>
                    </p>
                </div>
            </div>

            {{-- Description --}}
            <div>
                @include('admin.vitrine.partials.field', [
                    'label' => 'Description',
                    'name' => 'content[description]',
                    'value' => $c['description'] ?? '',
                    'type' => 'textarea',
                    'rows' => 4,
                    'placeholder' => 'Texte d\'accroche sous le titre principal…',
                ])
            </div>
        </div>
        </div>
    </section>

    {{-- ═══ SECTION 3 : Boutons d'action ═══ --}}
    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'buttons',
            'title' => 'Boutons d\'action',
            'subtitle' => 'Appels à l\'action sous la description',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-amber-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.buttons" x-cloak x-transition.opacity.duration.200ms>
        <div class="p-4 sm:p-6 space-y-4">
            <div class="flex justify-end">
                <button type="button" @click="buttons.push({label: '', href: '#contact', style: 'primary', icon: ''})"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-primary bg-primary/10 hover:bg-primary/15 border border-primary/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ajouter un bouton
                </button>
            </div>
            <template x-if="buttons.length === 0">
                <div class="text-center py-8 px-4 rounded-xl border-2 border-dashed border-border text-sm text-secondary">
                    Aucun bouton configuré
                </div>
            </template>

            <template x-for="(btn, index) in buttons" :key="index">
                <div class="rounded-xl border border-border bg-neutral-50/50 overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 bg-card border-b border-border/60">
                        <span class="text-xs font-bold text-primary uppercase tracking-wide" x-text="'Bouton ' + (index + 1)"></span>
                        <button type="button" @click="buttons.splice(index, 1)"
                                class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                title="Supprimer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Libellé</label>
                            <input type="text" :name="'content[buttons][' + index + '][label]'" x-model="btn.label"
                                   placeholder="Demander un devis" class="input-field w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Lien (ancre ou URL)</label>
                            <input type="text" :name="'content[buttons][' + index + '][href]'" x-model="btn.href"
                                   placeholder="#contact" class="input-field w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Style</label>
                            <select :name="'content[buttons][' + index + '][style]'" x-model="btn.style" class="input-field w-full text-sm">
                                <option value="primary">Primaire (plein)</option>
                                <option value="secondary">Secondaire (contour)</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Icône Font Awesome</label>
                            <input type="text" :name="'content[buttons][' + index + '][icon]'" x-model="btn.icon"
                                   placeholder="fas fa-calendar-alt" class="input-field w-full text-sm">
                        </div>
                    </div>
                    <div class="px-4 pb-4">
                        <p class="text-xs text-secondary mb-2">Aperçu</p>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium"
                              :class="btn.style === 'primary'
                                  ? 'bg-primary text-white'
                                  : (btn.style === 'whatsapp'
                                      ? 'bg-[#25D366] text-white'
                                      : 'bg-card border border-border text-primary')"
                              x-text="btn.label || 'Libellé du bouton'"></span>
                    </div>
                </div>
            </template>
        </div>
        </div>
    </section>

    {{-- ═══ SECTION 4 : Carte statistiques ═══ --}}
    <section class="rounded-2xl border border-primary/20 bg-gradient-to-br from-primary/5 via-card to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'card',
            'title' => 'Carte latérale & statistiques',
            'subtitle' => 'Encart flottant avec chiffres clés',
            'headerClass' => 'border-b border-primary/10',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.card" x-cloak x-transition.opacity.duration.200ms>
        <div class="p-4 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                @include('admin.vitrine.partials.field', [
                    'label' => 'Icône de la carte',
                    'name' => 'content[card][icon]',
                    'value' => $c['card']['icon'] ?? '',
                    'placeholder' => 'fas fa-tooth',
                ])
                @include('admin.vitrine.partials.field', [
                    'label' => 'Titre de la carte',
                    'name' => 'content[card][title]',
                    'value' => $c['card']['title'] ?? '',
                    'placeholder' => 'Prothèse Sur Mesure',
                ])
            </div>

            @include('admin.vitrine.partials.field', [
                'label' => 'Description de la carte',
                'name' => 'content[card][description]',
                'value' => $c['card']['description'] ?? '',
                'type' => 'textarea',
                'rows' => 2,
                'placeholder' => 'Courte description…',
            ])

            <div class="pt-2">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
                    <p class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                        Statistiques
                    </p>
                    <button type="button" @click="stats.push({value: '', label: ''})"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-primary bg-primary/10 hover:bg-primary/15 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Ajouter une stat
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    <template x-for="(stat, index) in stats" :key="index">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex items-center justify-end px-3 py-2 bg-neutral-50/80 border-b border-border/60">
                                <button type="button" @click="stats.splice(index, 1)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                        title="Supprimer cette statistique">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4 space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1">Valeur</label>
                                    <input type="text" :name="'content[card][stats][' + index + '][value]'" x-model="stat.value"
                                           placeholder="15+" class="input-field w-full text-sm font-bold">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1">Libellé</label>
                                    <input type="text" :name="'content[card][stats][' + index + '][label]'" x-model="stat.label"
                                           placeholder="Années d'expérience" class="input-field w-full text-sm">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        </div>
    </section>

</div>
