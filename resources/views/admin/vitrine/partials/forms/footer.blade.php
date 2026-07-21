@php
    $c = $content;
    $logoUrl = \App\Models\VitrineBlock::resolveImageAbsoluteUrl($c['logo_url'] ?? '');
    $logoSourceType = $c['logo_source_type'] ?? null;
    if (! $logoSourceType) {
        $logoSourceType = str_contains($c['logo_url'] ?? '', '/storage/vitrine/logo') ? 'upload' : 'url';
    }

    $defaultSocials = [
        ['label' => 'Facebook', 'url' => '', 'icon' => 'fab fa-facebook-f'],
        ['label' => 'Instagram', 'url' => '', 'icon' => 'fab fa-instagram'],
        ['label' => 'TikTok', 'url' => '', 'icon' => 'fab fa-tiktok'],
    ];

    $socialLinks = collect($c['social_links'] ?? [])->map(function ($social) {
        return [
            'label' => $social['label'] ?? '',
            'url' => $social['url'] ?? '',
            'icon' => $social['icon'] ?? '',
        ];
    })->values()->all();

    if ($socialLinks === []) {
        $socialLinks = $defaultSocials;
    }
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { logo: true, brand: true, socials: true, columns: true },
        logo: {
            image_url: @js($logoUrl),
            source_type: @js($logoSourceType),
            preview_url: null,
        },
        socials: @js($socialLinks),
        columns: @js($c['columns'] ?? []),
        logoPreview() {
            return this.logo.preview_url || this.logo.image_url || '';
        },
        onLogoFileChange(event) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (this.logo.preview_url?.startsWith('blob:')) {
                URL.revokeObjectURL(this.logo.preview_url);
            }
            this.logo.preview_url = URL.createObjectURL(file);
        },
        newSocial() {
            this.socials.push({ label: '', url: '', icon: 'fab fa-facebook-f' });
        },
     }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'logo',
            'title' => 'Logo du pied de page',
            'subtitle' => 'Affiché à gauche dans le pied de page',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-slate-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-slate-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.logo" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-5">
                @include('admin.vitrine.partials.logo-config-fields', [
                    'logoAlt' => $c['logo_alt'] ?? '',
                ])
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'brand',
            'title' => 'Marque & mentions',
            'subtitle' => 'Texte de présentation et copyright',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-slate-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-slate-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.brand" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-5">
                @include('admin.vitrine.partials.field', ['label' => 'Description de la marque', 'name' => 'content[brand_description]', 'value' => $c['brand_description'] ?? '', 'type' => 'textarea', 'rows' => 3])
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    @include('admin.vitrine.partials.field', ['label' => 'Copyright', 'name' => 'content[copyright]', 'value' => $c['copyright'] ?? '', 'placeholder' => 'LDM. Tous droits réservés.'])
                    @include('admin.vitrine.partials.field', ['label' => 'FAQ — libellé', 'name' => 'content[faq_link][label]', 'value' => $c['faq_link']['label'] ?? 'FAQ'])
                    @include('admin.vitrine.partials.field', ['label' => 'FAQ — URL', 'name' => 'content[faq_link][href]', 'value' => $c['faq_link']['href'] ?? '/faq'])
                    @include('admin.vitrine.partials.field', ['label' => 'Mentions légales — libellé', 'name' => 'content[legal_link][label]', 'value' => $c['legal_link']['label'] ?? ''])
                    @include('admin.vitrine.partials.field', ['label' => 'Mentions légales — URL', 'name' => 'content[legal_link][href]', 'value' => $c['legal_link']['href'] ?? ''])
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'socials',
            'title' => 'Réseaux sociaux',
            'subtitle' => 'Liens vers vos profils sociaux',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-slate-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.socials" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <div class="space-y-3" x-show="socials.length > 0">
                    <template x-for="(social, index) in socials" :key="'social-' + index">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <span class="text-xs font-bold text-primary uppercase tracking-wide" x-text="social.label || ('Réseau ' + (index + 1))"></span>
                                <button type="button" @click="socials.splice(index, 1)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Nom</label>
                                    <input type="text" :name="'content[social_links][' + index + '][label]'" x-model="social.label" placeholder="Facebook" class="input-field w-full text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Icône FA</label>
                                    <input type="text" :name="'content[social_links][' + index + '][icon]'" x-model="social.icon" placeholder="fab fa-facebook-f" class="input-field w-full text-sm font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">URL du profil</label>
                                    <input type="url" :name="'content[social_links][' + index + '][url]'" x-model="social.url" placeholder="https://…" class="input-field w-full text-sm">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter un réseau social', 'click' => 'newSocial()'])
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'columns',
            'title' => 'Colonnes du footer',
            'subtitle' => 'Groupes de liens en bas de page',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-slate-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.columns" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <div class="space-y-4" x-show="columns.length > 0">
                    <template x-for="(col, colIndex) in columns" :key="'col-' + colIndex">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <div class="flex-1 min-w-0">
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre de la colonne</label>
                                    <input type="text" :name="'content[columns][' + colIndex + '][title]'" x-model="col.title" placeholder="Services" class="input-field w-full text-sm font-semibold">
                                </div>
                                <button type="button" @click="columns.splice(colIndex, 1)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors self-end sm:self-auto"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4 border-l-4 border-primary/20 ml-4 sm:ml-6">
                                <button type="button" @click="col.links.push({label: '', href: '', icon: ''})"
                                        class="mb-3 text-xs font-semibold text-primary hover:text-primary/80 inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Ajouter un lien
                                </button>
                                <template x-for="(link, linkIndex) in col.links" :key="'link-' + colIndex + '-' + linkIndex">
                                    <div class="mb-3 p-3 rounded-lg bg-neutral-50/80 border border-border/40">
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-xs text-secondary mb-1">Libellé</label>
                                                <input type="text" :name="'content[columns][' + colIndex + '][links][' + linkIndex + '][label]'" x-model="link.label" class="input-field w-full text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-secondary mb-1">Lien</label>
                                                <input type="text" :name="'content[columns][' + colIndex + '][links][' + linkIndex + '][href]'" x-model="link.href" class="input-field w-full text-sm">
                                            </div>
                                            <div class="flex gap-2 items-end">
                                                <div class="flex-1 min-w-0">
                                                    <label class="block text-xs text-secondary mb-1">Icône (opt.)</label>
                                                    <input type="text" :name="'content[columns][' + colIndex + '][links][' + linkIndex + '][icon]'" x-model="link.icon" class="input-field w-full text-sm">
                                                </div>
                                                <button type="button" @click="col.links.splice(linkIndex, 1)" class="p-2 text-danger hover:bg-danger/10 rounded-lg flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une colonne', 'click' => "columns.push({title: '', links: []})"])
                </div>
            </div>
        </div>
    </section>
</div>
