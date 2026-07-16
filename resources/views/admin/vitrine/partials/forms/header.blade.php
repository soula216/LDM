@php
    $c = $content;
    $logoUrl = \App\Models\VitrineBlock::resolveImageAbsoluteUrl($c['logo_url'] ?? '');
    $logoSourceType = $c['logo_source_type'] ?? null;
    if (! $logoSourceType) {
        $logoSourceType = str_contains($c['logo_url'] ?? '', '/storage/vitrine/logo') ? 'upload' : 'url';
    }
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { logo: true, nav: true },
        logo: {
            image_url: @js($logoUrl),
            source_type: @js($logoSourceType),
            preview_url: null,
        },
        links: @js(collect($c['nav_links'] ?? [])->map(fn ($link) => [
            'label' => $link['label'] ?? '',
            'href' => $link['href'] ?? '',
            'is_active' => filter_var($link['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ])->values()->all()),
        clientSpaceIsActive: @js(filter_var($c['client_space_is_active'] ?? true, FILTER_VALIDATE_BOOLEAN)),
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
        }
    }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'logo',
            'title' => 'Logo de l\'en-tête',
            'subtitle' => 'Affiché dans la barre de navigation en haut du site',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-primary/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            'section' => 'nav',
            'title' => 'Navigation & espace client',
            'subtitle' => 'Liens du menu et bouton de connexion — activez ou désactivez chaque entrée',
            'headerClass' => 'border-b border-border/60',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.nav" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-6">
                <div class="space-y-3">
                    @include('admin.vitrine.partials.field', [
                        'label' => 'Libellé Espace client',
                        'name' => 'content[client_space_label]',
                        'value' => $c['client_space_label'] ?? '',
                    ])
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="hidden" name="content[client_space_is_active]" value="0">
                        <input type="checkbox" name="content[client_space_is_active]" value="1" x-model="clientSpaceIsActive"
                               class="rounded border-border text-primary focus:ring-primary/30">
                        <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher « Espace client » dans le menu</span>
                    </label>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-xs font-bold text-primary uppercase tracking-wider">Liens de navigation</h4>
                        <button type="button" @click="links.push({label: '', href: '', is_active: true})"
                                class="text-sm font-medium text-primary hover:text-primary/80 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Ajouter un lien
                        </button>
                    </div>
                    <template x-for="(link, index) in links" :key="index">
                        <div class="mb-3 p-4 rounded-xl border transition-colors"
                             :class="link.is_active ? 'bg-neutral-50/80 border-border/60' : 'bg-amber-50/20 border-amber-200/80'">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input type="text" :name="'content[nav_links][' + index + '][label]'" x-model="link.label" placeholder="Libellé" class="input-field flex-1">
                                <input type="text" :name="'content[nav_links][' + index + '][href]'" x-model="link.href" placeholder="#section ou /page" class="input-field flex-1">
                                <button type="button" @click="links.splice(index, 1)" class="px-3 py-2 text-danger hover:bg-danger/10 rounded-lg transition text-sm font-medium whitespace-nowrap">Supprimer</button>
                            </div>
                            <label class="mt-3 flex items-center gap-3 cursor-pointer group">
                                <input type="hidden" :name="'content[nav_links][' + index + '][is_active]'" value="0">
                                <input type="checkbox" :name="'content[nav_links][' + index + '][is_active]'" value="1" x-model="link.is_active"
                                       class="rounded border-border text-primary focus:ring-primary/30">
                                <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors"
                                      x-text="link.is_active ? 'Visible dans le menu' : 'Masqué dans le menu'"></span>
                            </label>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </section>
</div>
