@php
    $c = $content;
    $galleryCategories = collect($c['categories'] ?? [])->map(function ($category) {
        return [
            'key' => trim((string) ($category['key'] ?? '')),
            'label' => trim((string) ($category['label'] ?? '')),
        ];
    })->filter(fn (array $category): bool => $category['key'] !== '' || $category['label'] !== '')
      ->values()
      ->all();
    $galleryItems = collect($c['items'] ?? [])->map(function ($item) {
        return array_merge($item, [
            'image_url' => \App\Models\VitrineBlock::resolveImageAbsoluteUrl($item['image_url'] ?? ''),
            'preview_url' => null,
            'source_type' => ($item['source_type'] ?? null) ?: (
                str_contains($item['image_url'] ?? '', '/storage/vitrine/') ? 'upload' : 'url'
            ),
            'is_active' => filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_favorite' => filter_var($item['is_favorite'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'category' => trim((string) ($item['category'] ?? '')),
        ]);
    })->values()->all();
    $showAllOnSite = count($galleryItems) > 0
        && collect($galleryItems)->every(fn (array $item): bool => $item['is_active']);
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, categories: true, items: true },
        showAllOnSite: @js($showAllOnSite),
        lightbox: null,
        categories: @js($galleryCategories),
        items: @js($galleryItems),
        itemPreview(item) { return item.preview_url || item.image_url || ''; },
        updateShowAllFromItems() {
            this.showAllOnSite = this.items.length > 0 && this.items.every(item => item.is_active);
        },
        toggleShowAllOnSite() {
            this.items.forEach(item => {
                item.is_active = this.showAllOnSite;
                if (!this.showAllOnSite) {
                    item.is_favorite = false;
                }
            });
        },
        onItemActiveChange(item) {
            if (!item.is_active) {
                item.is_favorite = false;
            }
            this.updateShowAllFromItems();
        },
        openLightbox(item) {
            const src = this.itemPreview(item);
            if (!src) return;
            this.lightbox = {
                src,
                title: item.title || '',
                description: item.description || '',
            };
            document.body.classList.add('overflow-hidden');
        },
        closeLightbox() {
            this.lightbox = null;
            document.body.classList.remove('overflow-hidden');
        },
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
            this.updateShowAllFromItems();
        },
        categoryKey(index) {
            return 'categorie-' + (index + 1);
        },
        addCategory() {
            let index = this.categories.length;
            let key = this.categoryKey(index);
            while (this.categories.some(category => category.key === key)) {
                index++;
                key = this.categoryKey(index);
            }
            this.categories.push({ key, label: '' });
        },
        removeCategory(index) {
            const key = this.categories[index]?.key || '';
            this.categories.splice(index, 1);
            if (key !== '') {
                this.items.forEach(item => {
                    if (item.category === key) item.category = '';
                });
            }
        },
        addItem() {
            this.items.push({
                image_url: '',
                title: '',
                description: '',
                source_type: 'url',
                preview_url: null,
                is_active: this.showAllOnSite,
                is_favorite: false,
                category: this.categories[0]?.key || '',
            });
            this.updateShowAllFromItems();
        },
        itemStatus(item) {
            if (!item.is_active) return 'Masquée sur le site';
            if (item.is_favorite) return 'Visible sur le site · Mise en avant accueil';
            return 'Visible sur le site · Page galerie uniquement';
        }
     }"
     x-init="updateShowAllFromItems()">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'header',
            'title' => 'En-tête',
            'subtitle' => 'Titre et introduction de la galerie',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-purple-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-purple-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
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
            'section' => 'categories',
            'title' => 'Catégories',
            'subtitle' => 'Créez les catégories proposées pour chaque réalisation',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M3 3h7l11 11-7 7L3 10V3z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.categories" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="categories.length === 0">
                    <div class="text-center py-8 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune catégorie configurée</p>
                    </div>
                </template>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="categories.length > 0">
                    <template x-for="(category, index) in categories" :key="category.key">
                        <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
                            <input type="hidden"
                                   :name="'content[categories][' + index + '][key]'"
                                   x-model="category.key">
                            <div class="flex items-end gap-3">
                                <div class="flex-1 min-w-0">
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Nom de la catégorie</label>
                                    <input type="text"
                                           :name="'content[categories][' + index + '][label]'"
                                           x-model="category.label"
                                           class="input-field w-full text-sm"
                                           placeholder="Ex. Implantologie">
                                </div>
                                <button type="button"
                                        @click="removeCategory(index)"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-danger hover:bg-danger/10 border border-border hover:border-danger/20 transition-colors shrink-0"
                                        title="Supprimer la catégorie">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une catégorie', 'click' => 'addCategory()'])
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'items',
            'title' => 'Réalisations',
            'subtitle' => 'Activez et configurez chaque image de la galerie',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-purple-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.items" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <div class="mb-6 p-4 rounded-xl border border-border bg-neutral-50/80">
                    <input type="hidden" name="content[show_all_on_site]" value="0">
                    <label class="inline-flex items-start gap-3 cursor-pointer group w-full">
                        <input type="checkbox"
                               name="content[show_all_on_site]"
                               value="1"
                               x-model="showAllOnSite"
                               @change="toggleShowAllOnSite()"
                               class="w-5 h-5 mt-0.5 rounded-md border-border text-primary focus:ring-primary/30 transition shrink-0">
                        <span>
                            <span class="block text-sm font-semibold text-primary group-hover:text-primary/90 transition-colors">Afficher tous les images dans le site</span>
                            <span class="block text-xs text-secondary mt-1 leading-relaxed">
                                Coche ou décoche en temps réel l'option « Afficher cette image sur le site » pour chaque réalisation.
                            </span>
                        </span>
                    </label>
                </div>

                <template x-if="items.length === 0">
                    <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune réalisation configurée</p>
                    </div>
                </template>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" x-show="items.length > 0">
                    <template x-for="(item, index) in items" :key="'gallery-' + index">
                        <div class="rounded-xl border bg-card shadow-sm overflow-hidden flex flex-col transition-colors"
                             :class="item.is_active ? 'border-border' : 'border-amber-200/80 bg-amber-50/20'">
                            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 text-xs font-bold shrink-0" x-text="index + 1"></span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-primary truncate" x-text="(item.title || '').trim() !== '' ? item.title : ('Réalisation ' + (index + 1))"></p>
                                        <p class="text-xs text-secondary mt-0.5" x-text="itemStatus(item)"></p>
                                    </div>
                                </div>
                                <button type="button" @click="removeItem(index)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors shrink-0"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="aspect-[4/3] bg-neutral-100 relative overflow-hidden border-b border-border/40">
                                <template x-if="itemPreview(item)">
                                    <button type="button"
                                            @click="openLightbox(item)"
                                            class="group block w-full h-full p-0 border-0 bg-transparent cursor-zoom-in"
                                            title="Agrandir l'image">
                                        <img :src="itemPreview(item)" alt="" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
                                        <span class="absolute inset-x-0 bottom-0 px-3 py-2 text-xs font-medium text-white bg-gradient-to-t from-slate-900/75 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                            Cliquer pour agrandir
                                        </span>
                                    </button>
                                </template>
                                <template x-if="!itemPreview(item)">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-secondary/50 gap-2 p-4 text-center">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-xs font-medium">Ajoutez une image par URL ou upload</span>
                                    </div>
                                </template>
                            </div>

                            <div class="p-4 space-y-4 flex-1">
                                <label class="inline-flex items-center gap-3 cursor-pointer group pb-4 mb-4 border-b border-border/50 w-full">
                                    <input type="hidden" :name="'content[items][' + index + '][is_active]'" value="0">
                                    <input type="checkbox"
                                           :name="'content[items][' + index + '][is_active]'" value="1"
                                           x-model="item.is_active"
                                           @change="onItemActiveChange(item)"
                                           class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                                    <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher cette image sur le site</span>
                                </label>

                                <label class="inline-flex items-center gap-3 cursor-pointer group pb-4 mb-4 border-b border-border/50 w-full"
                                       :class="!item.is_active ? 'opacity-50 pointer-events-none' : ''">
                                    <input type="hidden" :name="'content[items][' + index + '][is_favorite]'" value="0">
                                    <input type="checkbox"
                                           :name="'content[items][' + index + '][is_favorite]'" value="1"
                                           x-model="item.is_favorite"
                                           :disabled="!item.is_active"
                                           class="w-5 h-5 rounded-md border-border text-amber-500 focus:ring-amber-400/30 transition">
                                    <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Favorite (aperçu page d'accueil)</span>
                                </label>

                                <input type="hidden" :name="'content[items][' + index + '][source_type]'" x-model="item.source_type">

                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="item.source_type = 'url'"
                                            :class="item.source_type === 'url' ? 'bg-primary text-white' : 'bg-card text-secondary border border-border'"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">URL</button>
                                    <button type="button" @click="item.source_type = 'upload'"
                                            :class="item.source_type === 'upload' ? 'bg-primary text-white' : 'bg-card text-secondary border border-border'"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">Upload</button>
                                </div>

                                <template x-if="item.source_type === 'url'">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">URL de l'image</label>
                                        <input type="text" :name="'content[items][' + index + '][image_url]'" x-model="item.image_url" placeholder="https://…" class="input-field w-full text-sm font-mono">
                                    </div>
                                </template>

                                <template x-if="item.source_type === 'upload'">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Fichier image</label>
                                        <input type="hidden" :name="'content[items][' + index + '][image_url]'" :value="item.image_url">
                                        <label class="flex flex-col items-center justify-center w-full px-4 py-5 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer transition-all">
                                            <input type="file" :name="'gallery_uploads[' + index + ']'" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" class="sr-only" @change="onItemFileChange($event, item)">
                                            <span class="text-sm font-medium text-primary">Choisir une image</span>
                                            <span class="text-xs text-secondary mt-1">JPEG, PNG, WebP, GIF — max 5 Mo</span>
                                        </label>
                                    </div>
                                </template>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Catégorie</label>
                                    <select :name="'content[items][' + index + '][category]'"
                                            x-model="item.category"
                                            class="input-field w-full text-sm">
                                        <option value="">Sans catégorie</option>
                                        <template x-for="category in categories" :key="category.key">
                                            <option :value="category.key"
                                                    x-text="(category.label || '').trim() !== '' ? category.label : 'Catégorie sans nom'"></option>
                                        </template>
                                    </select>
                                    <p class="mt-1.5 text-xs text-secondary" x-show="categories.length === 0">
                                        Ajoutez d’abord une catégorie dans le bloc « Catégories ».
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                    <input type="text" :name="'content[items][' + index + '][title]'" x-model="item.title" placeholder="Titre de la réalisation" class="input-field w-full text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description</label>
                                    <textarea :name="'content[items][' + index + '][description]'" x-model="item.description" rows="2" placeholder="Courte description…" class="input-field w-full text-sm resize-y"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une réalisation', 'click' => 'addItem()'])
                </div>
            </div>
        </div>
    </section>

    <div x-show="lightbox"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closeLightbox()"
         class="fixed inset-0 z-[120] flex items-center justify-center p-3 sm:p-5"
         role="dialog"
         aria-modal="true"
         :aria-label="lightbox?.title || 'Aperçu image galerie'">
        <button type="button"
                class="absolute inset-0 bg-slate-950/90 backdrop-blur-md"
                @click="closeLightbox()"
                aria-label="Fermer"></button>
        <div class="relative z-10 w-full max-w-[min(96vw,1500px)] h-[min(94dvh,100%)] flex flex-col gap-3">
            <div class="flex items-center justify-between gap-3 px-1">
                <div class="inline-flex items-center gap-2 min-w-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-[0.14em] text-slate-200 bg-white/10 border border-white/15">Galerie</span>
                    <span class="text-xs font-semibold text-slate-300/85" x-text="lightbox ? 'Aperçu' : ''"></span>
                </div>
                <button type="button"
                        @click="closeLightbox()"
                        class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white/10 border border-white/15 text-white hover:bg-white/18 transition-colors"
                        aria-label="Fermer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <figure class="relative flex-1 min-h-0 flex flex-col items-center justify-center rounded-[22px] border border-white/12 bg-gradient-to-br from-slate-900/70 to-slate-800/55 shadow-[0_30px_80px_rgba(0,0,0,0.55)] p-2 sm:p-3 overflow-hidden">
                <div class="absolute inset-[10%] bg-sky-400/10 blur-3xl rounded-full pointer-events-none" aria-hidden="true"></div>
                <img :src="lightbox?.src"
                     :alt="lightbox?.title || 'Image galerie'"
                     class="relative z-10 max-h-[calc(94dvh-10rem)] w-auto max-w-full rounded-2xl object-contain shadow-2xl bg-slate-950">
                <figcaption class="relative z-10 mt-3 w-full max-w-3xl text-center px-3 py-3 rounded-2xl bg-white/6 border border-white/10 backdrop-blur-md"
                            x-show="lightbox?.title || lightbox?.description">
                    <h3 class="text-base sm:text-lg font-semibold text-white" x-text="lightbox?.title" x-show="lightbox?.title"></h3>
                    <p class="text-sm text-slate-200/85 mt-1 leading-relaxed" x-text="lightbox?.description" x-show="lightbox?.description"></p>
                </figcaption>
            </figure>
        </div>
    </div>
</div>
