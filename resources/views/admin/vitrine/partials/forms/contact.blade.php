@php
    $c = $content;
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
        open: { intro: true, items: true },
        items: @js($contactItems)
     }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'intro',
            'title' => 'Introduction',
            'subtitle' => 'Texte d\'accroche de la section contact',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-teal-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-teal-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.intro" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    @include('admin.vitrine.partials.field', ['label' => 'Icône du tag', 'name' => 'content[tag_icon]', 'value' => $c['tag_icon'] ?? '', 'placeholder' => 'fas fa-comments'])
                    @include('admin.vitrine.partials.field', ['label' => 'Texte du tag', 'name' => 'content[tag_text]', 'value' => $c['tag_text'] ?? '', 'placeholder' => 'Contactez-nous'])
                    @include('admin.vitrine.partials.field', ['label' => 'Titre principal', 'name' => 'content[title]', 'value' => $c['title'] ?? '', 'placeholder' => 'Prêt à démarrer votre projet ?'])
                    @include('admin.vitrine.partials.field', ['label' => 'Titre du formulaire', 'name' => 'content[form_title]', 'value' => $c['form_title'] ?? '', 'placeholder' => 'Demander un devis'])
                </div>
                @include('admin.vitrine.partials.field', ['label' => 'Description', 'name' => 'content[description]', 'value' => $c['description'] ?? '', 'type' => 'textarea', 'rows' => 3])
                @include('admin.vitrine.partials.field', ['label' => 'Libellé du bouton d\'envoi', 'name' => 'content[form_submit_label]', 'value' => $c['form_submit_label'] ?? '', 'placeholder' => 'Envoyer ma demande'])
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'items',
            'title' => 'Coordonnées',
            'subtitle' => 'Adresse, téléphone, email, horaires…',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-teal-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.items" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
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

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une coordonnée', 'click' => "items.push({icon: '', title: '', value_1: '', value_2: ''})"])
                </div>
            </div>
        </div>
    </section>
</div>
