@php $c = $content; @endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { content: true, list: true, card: true },
        list: @js($c['list'] ?? [])
     }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'content',
            'title' => 'Contenu principal',
            'subtitle' => 'Titre, description et arguments clés',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-amber-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.content" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-6">
                <div class="p-4 sm:p-5 rounded-xl bg-neutral-50/80 border border-border/60">
                    <p class="text-xs font-bold text-primary uppercase tracking-wider mb-4">Titre de la section</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                        @include('admin.vitrine.partials.field', ['label' => 'Avant surbrillance', 'name' => 'content[title_before]', 'value' => $c['title_before'] ?? '', 'placeholder' => 'Pourquoi Choisir'])
                        @include('admin.vitrine.partials.field', ['label' => 'Surbrillance', 'name' => 'content[title_highlight]', 'value' => $c['title_highlight'] ?? '', 'placeholder' => 'LDM'])
                        @include('admin.vitrine.partials.field', ['label' => 'Après surbrillance', 'name' => 'content[title_after]', 'value' => $c['title_after'] ?? '', 'placeholder' => '?'])
                    </div>
                    <div class="mt-4 p-3 sm:p-4 rounded-lg bg-card border border-border/40">
                        <p class="text-xs text-secondary mb-1">Aperçu du titre</p>
                        <p class="text-lg sm:text-xl font-bold text-primary leading-tight">
                            {{ $c['title_before'] ?? '' }} <span class="text-primary/70">{{ $c['title_highlight'] ?? '' }}</span>{{ $c['title_after'] ?? '' }}
                        </p>
                    </div>
                </div>

                @include('admin.vitrine.partials.field', ['label' => 'Description', 'name' => 'content[description]', 'value' => $c['description'] ?? '', 'type' => 'textarea', 'rows' => 4, 'placeholder' => 'Texte de présentation…'])
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'list',
            'title' => 'Points forts',
            'subtitle' => 'Liste à puces affichée sur le site',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.list" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <div class="space-y-3" x-show="list.length > 0">
                    <template x-for="(item, index) in list" :key="'point-' + index">
                        <div class="flex flex-col sm:flex-row gap-2 sm:items-center p-3 rounded-xl bg-card border border-border/60">
                            <span class="hidden sm:inline-flex items-center justify-center w-7 h-7 rounded-lg bg-primary/10 text-primary text-xs font-bold flex-shrink-0" x-text="index + 1"></span>
                            <input type="text" :name="'content[list][' + index + ']'" x-model="list[index]" placeholder="Point fort…" class="input-field flex-1 text-sm min-w-0">
                            <button type="button" @click="list.splice(index, 1)"
                                    class="self-end sm:self-auto inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                    title="Supprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <template x-if="list.length === 0">
                    <div class="text-center py-8 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary">Aucun point fort</p>
                    </div>
                </template>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter un point fort', 'click' => "list.push('')"])
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-primary/20 bg-gradient-to-br from-primary/5 via-card to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'card',
            'title' => 'Carte certification',
            'subtitle' => 'Encart mis en avant à droite',
            'headerClass' => 'border-b border-primary/10',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.card" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    @include('admin.vitrine.partials.field', ['label' => 'Icône', 'name' => 'content[card][icon]', 'value' => $c['card']['icon'] ?? '', 'placeholder' => 'fas fa-award'])
                    @include('admin.vitrine.partials.field', ['label' => 'Titre', 'name' => 'content[card][title]', 'value' => $c['card']['title'] ?? '', 'placeholder' => 'Certification ISO'])
                    @include('admin.vitrine.partials.field', ['label' => 'Description', 'name' => 'content[card][description]', 'value' => $c['card']['description'] ?? '', 'placeholder' => 'Courte description…'])
                </div>
            </div>
        </div>
    </section>
</div>
