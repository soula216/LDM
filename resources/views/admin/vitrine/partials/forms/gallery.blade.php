@php
    $c = $content;
    $galleryItems = collect($c['items'] ?? [])->map(function ($item) {
        return array_merge($item, [
            'image_url' => \App\Models\VitrineBlock::resolveImageAbsoluteUrl($item['image_url'] ?? ''),
            'preview_url' => null,
            'source_type' => ($item['source_type'] ?? null) ?: (
                str_contains($item['image_url'] ?? '', '/storage/vitrine/') ? 'upload' : 'url'
            ),
        ]);
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, items: true },
        items: @js($galleryItems),
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
        addItem() {
            this.items.push({ image_url: '', title: '', description: '', source_type: 'url', preview_url: null });
        }
     }">

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
            'section' => 'items',
            'title' => 'Réalisations',
            'subtitle' => 'Images et légendes de la galerie',
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
                <template x-if="items.length === 0">
                    <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune réalisation configurée</p>
                    </div>
                </template>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" x-show="items.length > 0">
                    <template x-for="(item, index) in items" :key="'gallery-' + index">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden flex flex-col">
                            <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <span class="text-xs font-bold text-primary uppercase tracking-wide" x-text="'Réalisation ' + (index + 1)"></span>
                                <button type="button" @click="removeItem(index)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="aspect-[4/3] bg-neutral-100 relative overflow-hidden border-b border-border/40">
                                <template x-if="itemPreview(item)">
                                    <img :src="itemPreview(item)" alt="" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!itemPreview(item)">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-secondary/50 gap-2 p-4 text-center">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-xs font-medium">Ajoutez une image par URL ou upload</span>
                                    </div>
                                </template>
                            </div>

                            <div class="p-4 space-y-4 flex-1">
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
</div>
