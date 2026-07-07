@php
    $c = $content;
    $infoIsActive = filter_var($c['info_is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $formIsActive = filter_var($c['form_is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $mapIsActive = filter_var($c['map_is_active'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $contactItems = collect($c['items'] ?? [])->map(function ($item) {
        return [
            'icon' => $item['icon'] ?? '',
            'title' => $item['title'] ?? '',
            'value_1' => $item['value_1'] ?? $item['value'] ?? '',
            'value_2' => $item['value_2'] ?? '',
        ];
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { info: true, form: true, map: true },
        items: @js($contactItems),
        infoIsActive: @js($infoIsActive),
        formIsActive: @js($formIsActive),
        mapIsActive: @js($mapIsActive),
     }">

    {{-- Bloc gauche sur le site : informations & coordonnées --}}
    <section class="rounded-2xl border bg-card overflow-hidden transition-colors"
             :class="infoIsActive ? 'border-border' : 'border-amber-200/80 bg-amber-50/20'">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'info',
            'title' => 'Bloc informations',
            'subtitle' => 'Carte de gauche — tag, titre, description et coordonnées',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-teal-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-teal-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            @endslot
            @slot('actions')
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="content[info_is_active]" value="0">
                    <input type="checkbox"
                           name="content[info_is_active]"
                           value="1"
                           x-model="infoIsActive"
                           class="w-4 h-4 rounded border-border text-primary focus:ring-primary/30 transition">
                    <span class="text-xs font-medium text-secondary whitespace-nowrap">Afficher sur le site</span>
                </label>
            @endslot
        @endcomponent

        <div x-show="open.info" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-6">
                <div class="p-4 sm:p-5 rounded-xl bg-neutral-50/80 border border-border/60 space-y-5">
                    <p class="text-xs font-bold text-primary uppercase tracking-wider">En-tête</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        @include('admin.vitrine.partials.field', ['label' => 'Icône du tag', 'name' => 'content[tag_icon]', 'value' => $c['tag_icon'] ?? '', 'placeholder' => 'fas fa-comments'])
                        @include('admin.vitrine.partials.field', ['label' => 'Texte du tag', 'name' => 'content[tag_text]', 'value' => $c['tag_text'] ?? '', 'placeholder' => 'Contactez-nous'])
                    </div>
                    @include('admin.vitrine.partials.field', ['label' => 'Titre principal', 'name' => 'content[title]', 'value' => $c['title'] ?? '', 'placeholder' => 'Prêt à démarrer votre projet ?'])
                    @include('admin.vitrine.partials.field', ['label' => 'Description', 'name' => 'content[description]', 'value' => $c['description'] ?? '', 'type' => 'textarea', 'rows' => 3])
                </div>

                <div class="p-4 sm:p-5 rounded-xl bg-neutral-50/80 border border-border/60 space-y-4">
                    <div>
                        <p class="text-xs font-bold text-primary uppercase tracking-wider">Coordonnées</p>
                        <p class="text-sm text-secondary mt-1">Adresse, téléphone, email, horaires…</p>
                    </div>

                    <div class="space-y-4" x-show="items.length > 0">
                        <template x-for="(item, index) in items" :key="'contact-' + index">
                            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                    <span class="text-xs font-bold text-primary uppercase tracking-wide" x-text="'Coordonnée ' + (index + 1)"></span>
                                    <button type="button" @click="items.splice(index, 1)"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                            title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Icône</label>
                                        <input type="text" :name="'content[items][' + index + '][icon]'" x-model="item.icon" placeholder="fas fa-phone" class="input-field w-full text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                        <input type="text" :name="'content[items][' + index + '][title]'" x-model="item.title" placeholder="Téléphone" class="input-field w-full text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Valeur 1</label>
                                        <input type="text" :name="'content[items][' + index + '][value_1]'" x-model="item.value_1" placeholder="+33 1 23 45 67 89" class="input-field w-full text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Valeur 2</label>
                                        <input type="text" :name="'content[items][' + index + '][value_2]'" x-model="item.value_2" placeholder="Ligne complémentaire (optionnel)" class="input-field w-full text-sm">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="pt-2 border-t border-border/60">
                        @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une coordonnée', 'click' => "items.push({icon: '', title: '', value_1: '', value_2: ''})"])
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Bloc droit sur le site : formulaire de contact --}}
    <section class="rounded-2xl border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden transition-colors"
             :class="formIsActive ? 'border-border' : 'border-amber-200/80 bg-amber-50/20'">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'form',
            'title' => 'Bloc formulaire',
            'subtitle' => 'Carte de droite — titre, sous-titre et bouton d\'envoi',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endslot
            @slot('actions')
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="content[form_is_active]" value="0">
                    <input type="checkbox"
                           name="content[form_is_active]"
                           value="1"
                           x-model="formIsActive"
                           class="w-4 h-4 rounded border-border text-primary focus:ring-primary/30 transition">
                    <span class="text-xs font-medium text-secondary whitespace-nowrap">Afficher sur le site</span>
                </label>
            @endslot
        @endcomponent

        <div x-show="open.form" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-5">
                @include('admin.vitrine.partials.field', ['label' => 'Titre du formulaire', 'name' => 'content[form_title]', 'value' => $c['form_title'] ?? '', 'placeholder' => 'Demander un devis personnalisé'])
                @include('admin.vitrine.partials.field', ['label' => 'Sous-titre du formulaire', 'name' => 'content[form_subtitle]', 'value' => $c['form_subtitle'] ?? '', 'placeholder' => 'Réponse sous 24 h ouvrées'])
                @include('admin.vitrine.partials.field', ['label' => 'Libellé du bouton d\'envoi', 'name' => 'content[form_submit_label]', 'value' => $c['form_submit_label'] ?? '', 'placeholder' => 'Envoyer ma demande'])

                <div class="p-4 rounded-xl bg-card border border-border/60">
                    <p class="text-xs text-secondary mb-2">Aperçu de l'en-tête du formulaire</p>
                    <p class="text-lg font-bold text-primary">{{ $c['form_title'] ?? 'Demander un devis personnalisé' }}</p>
                    <p class="text-sm text-secondary mt-1">{{ $c['form_subtitle'] ?? 'Réponse sous 24 h ouvrées' }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Bloc carte Google Maps --}}
    <section class="rounded-2xl border bg-card overflow-hidden transition-colors"
             :class="mapIsActive ? 'border-border' : 'border-amber-200/80 bg-amber-50/20'">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'map',
            'title' => 'Bloc carte Google Maps',
            'subtitle' => 'Affichage de l\'adresse sur une carte interactive',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-emerald-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                </div>
            @endslot
            @slot('actions')
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="content[map_is_active]" value="0">
                    <input type="checkbox"
                           name="content[map_is_active]"
                           value="1"
                           x-model="mapIsActive"
                           class="w-4 h-4 rounded border-border text-primary focus:ring-primary/30 transition">
                    <span class="text-xs font-medium text-secondary whitespace-nowrap">Afficher sur le site</span>
                </label>
            @endslot
        @endcomponent

        <div x-show="open.map" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-5">
                @include('admin.vitrine.partials.field', ['label' => 'Titre de la carte', 'name' => 'content[map_title]', 'value' => $c['map_title'] ?? '', 'placeholder' => 'Notre localisation'])
                @include('admin.vitrine.partials.field', ['label' => 'Adresse à afficher', 'name' => 'content[map_address]', 'value' => $c['map_address'] ?? '', 'type' => 'textarea', 'rows' => 2, 'placeholder' => '123 Avenue de la Dentisterie, 75001 Paris, France'])
                @include('admin.vitrine.partials.field', ['label' => 'URL d\'intégration Google Maps (optionnel)', 'name' => 'content[map_embed_url]', 'value' => $c['map_embed_url'] ?? '', 'placeholder' => 'https://www.google.com/maps/embed?pb=...'])

                <div class="p-4 rounded-xl bg-neutral-50/80 border border-border/60">
                    <p class="text-xs font-semibold text-secondary uppercase tracking-wide mb-2">Comment configurer la carte</p>
                    <ul class="text-sm text-secondary space-y-1.5 list-disc list-inside">
                        <li>Renseignez l'<strong>adresse complète</strong> : la carte sera générée automatiquement.</li>
                        <li>Ou collez l'URL <strong>src</strong> d'une iframe depuis Google Maps → Partager → Intégrer une carte.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>
