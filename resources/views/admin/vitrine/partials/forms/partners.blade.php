@php
    $c = $content;
    $partnerItems = collect($c['items'] ?? [])->map(function ($item) {
        return array_merge($item, [
            'image_url' => \App\Models\VitrineBlock::resolveImageAbsoluteUrl($item['image_url'] ?? ''),
            'preview_url' => null,
            'source_type' => ($item['source_type'] ?? null) ?: (
                str_contains($item['image_url'] ?? '', '/storage/vitrine/partners') ? 'upload' : 'url'
            ),
            'is_active' => filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, items: true },
        items: @js($partnerItems),
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
            this.items.push({
                name: '',
                url: '',
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
            'subtitle' => 'Titre et introduction de la section partenaires',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-indigo-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-indigo-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.header" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                @include('admin.vitrine.partials.section-en-tete', ['c' => $c, 'color' => 'indigo'])
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'items',
            'title' => 'Logos partenaires',
            'subtitle' => 'Ajoutez les logos affichés dans le carrousel de la page d\'accueil',
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

        <div x-show="open.items" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="items.length === 0">
                    <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucun partenaire — ajoutez des logos pour le carrousel</p>
                    </div>
                </template>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" x-show="items.length > 0">
                    <template x-for="(item, index) in items" :key="'partner-' + index">
                        <div class="rounded-xl border bg-card shadow-sm overflow-hidden flex flex-col transition-colors"
                             :class="item.is_active ? 'border-border' : 'border-amber-200/80 bg-amber-50/20'">
                            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 text-xs font-bold shrink-0" x-text="index + 1"></span>
                                    <p class="text-sm font-semibold text-primary truncate" x-text="(item.name || '').trim() !== '' ? item.name : ('Partenaire ' + (index + 1))"></p>
                                </div>
                                <button type="button" @click="removeItem(index)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors shrink-0"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="h-36 bg-neutral-100 relative overflow-hidden border-b border-border/40 flex items-center justify-center p-6">
                                <template x-if="itemPreview(item)">
                                    <img :src="itemPreview(item)" :alt="item.name || 'Logo partenaire'" class="max-h-full max-w-full object-contain">
                                </template>
                                <template x-if="!itemPreview(item)">
                                    <span class="text-xs text-secondary">Logo à ajouter</span>
                                </template>
                            </div>

                            <div class="p-4 space-y-4 flex-1">
                                <label class="inline-flex items-center gap-3 cursor-pointer group w-full">
                                    <input type="hidden" :name="'content[items][' + index + '][is_active]'" value="0">
                                    <input type="checkbox"
                                           :name="'content[items][' + index + '][is_active]'" value="1"
                                           x-model="item.is_active"
                                           class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                                    <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher sur le site</span>
                                </label>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Nom du partenaire</label>
                                    <input type="text"
                                           :name="'content[items][' + index + '][name]'"
                                           x-model="item.name"
                                           placeholder="Nom de l'entreprise"
                                           class="input-field w-full text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Lien (optionnel)</label>
                                    <input type="url"
                                           :name="'content[items][' + index + '][url]'"
                                           x-model="item.url"
                                           placeholder="https://exemple.com"
                                           class="input-field w-full text-sm font-mono">
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
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">URL du logo</label>
                                        <input type="text"
                                               :name="'content[items][' + index + '][image_url]'"
                                               x-model="item.image_url"
                                               placeholder="https://exemple.com/logo.png"
                                               class="input-field w-full text-sm font-mono">
                                    </div>
                                </template>

                                <template x-if="item.source_type === 'upload'">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Fichier logo</label>
                                        <input type="hidden" :name="'content[items][' + index + '][image_url]'" :value="item.image_url">
                                        <label class="flex flex-col items-center justify-center w-full min-h-[88px] px-4 py-4 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer transition-all">
                                            <input type="file"
                                                   :name="'partner_uploads[' + index + ']'"
                                                   accept="image/jpeg,image/jpg,image/png,image/webp,image/gif,image/svg+xml"
                                                   class="sr-only"
                                                   @change="onItemFileChange($event, item)">
                                            <span class="text-sm font-medium text-primary">Cliquez pour choisir un logo</span>
                                            <span class="text-xs text-secondary mt-1">JPEG, PNG, WebP, GIF, SVG — max 5 Mo</span>
                                        </label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter un partenaire', 'click' => 'addItem()'])
                </div>
            </div>
        </div>
    </section>
</div>
