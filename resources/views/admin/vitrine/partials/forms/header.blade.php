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
        links: @js($c['nav_links'] ?? []),
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
            'subtitle' => 'Liens du menu et bouton de connexion',
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
                @include('admin.vitrine.partials.field', [
                    'label' => 'Libellé Espace client',
                    'name' => 'content[client_space_label]',
                    'value' => $c['client_space_label'] ?? '',
                ])

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-xs font-bold text-primary uppercase tracking-wider">Liens de navigation</h4>
                        <button type="button" @click="links.push({label: '', href: ''})"
                                class="text-sm font-medium text-primary hover:text-primary/80 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Ajouter un lien
                        </button>
                    </div>
                    <template x-for="(link, index) in links" :key="index">
                        <div class="flex flex-col sm:flex-row gap-3 mb-3 p-4 rounded-xl bg-neutral-50/80 border border-border/60">
                            <input type="text" :name="'content[nav_links][' + index + '][label]'" x-model="link.label" placeholder="Libellé" class="input-field flex-1">
                            <input type="text" :name="'content[nav_links][' + index + '][href]'" x-model="link.href" placeholder="#section" class="input-field flex-1">
                            <button type="button" @click="links.splice(index, 1)" class="px-3 py-2 text-danger hover:bg-danger/10 rounded-lg transition text-sm font-medium">Supprimer</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </section>
</div>
