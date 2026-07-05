@php $c = $content; @endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, items: true },
        items: @js($c['items'] ?? [])
     }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'header',
            'title' => 'En-tête',
            'subtitle' => 'Titre et introduction de la section FAQ',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-violet-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-violet-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
            'title' => 'Questions & réponses',
            'subtitle' => 'Liste des questions affichées sur la page d\'accueil',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-violet-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.items" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="items.length === 0">
                    <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune question configurée</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="items.length > 0">
                    <template x-for="(item, index) in items" :key="'faq-' + index">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <span class="text-xs font-bold text-primary uppercase tracking-wide" x-text="'Question ' + (index + 1)"></span>
                                <button type="button" @click="items.splice(index, 1)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4 sm:p-5 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Question</label>
                                    <input type="text" :name="'content[items][' + index + '][question]'" x-model="item.question" placeholder="Comment envoyer un fichier STL ?" class="input-field w-full text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Réponse</label>
                                    <textarea :name="'content[items][' + index + '][answer]'" x-model="item.answer" rows="4" placeholder="Rédigez la réponse…" class="input-field w-full text-sm resize-y"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une question', 'click' => "items.push({question: '', answer: ''})"])
                </div>
            </div>
        </div>
    </section>
</div>
