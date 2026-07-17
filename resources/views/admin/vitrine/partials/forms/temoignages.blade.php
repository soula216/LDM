@php
    $c = $content;
    $temoignageItems = collect($c['items'] ?? [])->map(function ($item) {
        return array_merge($item, [
            'image_url' => \App\Models\VitrineBlock::resolveImageAbsoluteUrl($item['image_url'] ?? ''),
            'preview_url' => null,
            'source_type' => ($item['source_type'] ?? null) ?: (
                str_contains($item['image_url'] ?? '', '/storage/vitrine/temoignages') ? 'upload' : 'url'
            ),
            'is_active' => filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, items: true },
        items: @js($temoignageItems),
        itemPreview(item) { return item.preview_url || item.image_url || ''; },
        onItemFileChange(event, item) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (item.preview_url?.startsWith('blob:')) URL.revokeObjectURL(item.preview_url);
            item.preview_url = URL.createObjectURL(file);
        },
        removeItem(index) {
            const item = this.items[index];
            if (item?.preview_url?.startsWith('blob:')) URL.revokeObjectURL(item.preview_url);
            this.items.splice(index, 1);
        },
        moveItem(index, direction) {
            const target = index + direction;
            if (target < 0 || target >= this.items.length) return;
            const [moved] = this.items.splice(index, 1);
            this.items.splice(target, 0, moved);
        },
        addItem() {
            this.items.push({
                name: '',
                title: '',
                comment: '',
                image_url: '',
                source_type: 'url',
                preview_url: null,
                is_active: true,
            });
        },
     }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'header',
            'title' => 'En-tête',
            'subtitle' => 'Titre et introduction de la section témoignages',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-amber-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.header" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                @include('admin.vitrine.partials.section-en-tete', ['c' => $c])
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'items',
            'title' => 'Avis des dentistes',
            'subtitle' => 'Photo, nom et commentaire — la note de 5 étoiles est affichée automatiquement',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.items" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="items.length === 0">
                    <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucun témoignage — ajoutez les avis de vos dentistes partenaires</p>
                    </div>
                </template>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" x-show="items.length > 0">
                    <template x-for="(item, index) in items" :key="'temoignage-' + index">
                        <div class="rounded-xl border bg-card shadow-sm overflow-hidden flex flex-col transition-colors"
                             :class="item.is_active ? 'border-border' : 'border-amber-200/80 bg-amber-50/20'">
                            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 text-xs font-bold shrink-0" x-text="index + 1"></span>
                                    <p class="text-sm font-semibold text-primary truncate" x-text="(item.name || '').trim() !== '' ? item.name : ('Témoignage ' + (index + 1))"></p>
                                </div>
                                <div class="flex items-center gap-0.5 shrink-0">
                                    <button type="button" @click="moveItem(index, -1)" :disabled="index === 0"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-secondary hover:text-primary hover:bg-neutral-100 disabled:opacity-30 transition-colors"
                                            title="Monter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" @click="moveItem(index, 1)" :disabled="index === items.length - 1"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-secondary hover:text-primary hover:bg-neutral-100 disabled:opacity-30 transition-colors"
                                            title="Descendre">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" @click="removeItem(index)"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                            title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="p-4 space-y-4 flex-1">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-neutral-100 border border-border/60 overflow-hidden flex items-center justify-center shrink-0">
                                        <template x-if="itemPreview(item)">
                                            <img :src="itemPreview(item)" :alt="item.name || 'Photo du dentiste'" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!itemPreview(item)">
                                            <svg class="w-7 h-7 text-neutral-300" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                                            </svg>
                                        </template>
                                    </div>
                                    <div class="flex items-center gap-1 text-amber-400" title="Note affichée : 5 étoiles">
                                        @for($i = 0; $i < 5; $i++)
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                        <span class="ml-1 text-[11px] font-semibold text-secondary">5/5 (auto)</span>
                                    </div>
                                </div>

                                <label class="inline-flex items-center gap-3 cursor-pointer group w-full">
                                    <input type="hidden" :name="'content[items][' + index + '][is_active]'" value="0">
                                    <input type="checkbox"
                                           :name="'content[items][' + index + '][is_active]'" value="1"
                                           x-model="item.is_active"
                                           class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                                    <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher sur le site</span>
                                </label>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Nom du dentiste</label>
                                    <input type="text"
                                           :name="'content[items][' + index + '][name]'"
                                           x-model="item.name"
                                           placeholder="Dr. Sophie Martin"
                                           class="input-field w-full text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Fonction / Cabinet (optionnel)</label>
                                    <input type="text"
                                           :name="'content[items][' + index + '][title]'"
                                           x-model="item.title"
                                           placeholder="Chirurgien-dentiste — Tunis"
                                           class="input-field w-full text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Commentaire</label>
                                    <textarea :name="'content[items][' + index + '][comment]'"
                                              x-model="item.comment"
                                              rows="4"
                                              placeholder="Un service irréprochable, des prothèses d'une précision remarquable…"
                                              class="input-field w-full text-sm resize-y"></textarea>
                                </div>

                                <input type="hidden" :name="'content[items][' + index + '][source_type]'" x-model="item.source_type">

                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="item.source_type = 'url'"
                                            :class="item.source_type === 'url' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                        URL
                                    </button>
                                    <button type="button" @click="item.source_type = 'upload'"
                                            :class="item.source_type === 'upload' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                        Upload
                                    </button>
                                </div>

                                <template x-if="item.source_type === 'url'">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">URL de la photo</label>
                                        <input type="text"
                                               :name="'content[items][' + index + '][image_url]'"
                                               x-model="item.image_url"
                                               placeholder="https://exemple.com/photo.jpg"
                                               class="input-field w-full text-sm font-mono">
                                    </div>
                                </template>

                                <template x-if="item.source_type === 'upload'">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Photo du dentiste</label>
                                        <input type="hidden" :name="'content[items][' + index + '][image_url]'" :value="item.image_url">
                                        <label class="flex flex-col items-center justify-center w-full min-h-[88px] px-4 py-4 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer transition-all">
                                            <input type="file"
                                                   :name="'temoignage_uploads[' + index + ']'"
                                                   accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                                                   class="sr-only"
                                                   @change="onItemFileChange($event, item)">
                                            <span class="text-sm font-medium text-primary">Cliquez pour choisir une photo</span>
                                            <span class="text-xs text-secondary mt-1">JPEG, PNG, WebP, GIF — max 5 Mo</span>
                                        </label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter un témoignage', 'click' => 'addItem()'])
                </div>
            </div>
        </div>
    </section>
</div>
