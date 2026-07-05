@php
    $c = $content;
    $categories = [
        'catalogue' => 'Catalogues',
        'guide' => 'Guides techniques',
        'protocole' => 'Protocoles',
        'notice' => 'Notices',
    ];
    $documents = collect($c['documents'] ?? [])->map(function ($doc) {
        $fileUrl = trim((string) ($doc['file_url'] ?? ''));
        $coverUrl = trim((string) ($doc['cover_image_url'] ?? ''));
        $coverSourceType = $doc['cover_image_source_type'] ?? null;

        if (! $coverSourceType) {
            $coverSourceType = $coverUrl !== ''
                ? (str_contains($coverUrl, '/storage/vitrine/academy/covers') ? 'upload' : 'url')
                : 'url';
        }

        return [
            'title' => $doc['title'] ?? '',
            'category' => $doc['category'] ?? 'catalogue',
            'description' => $doc['description'] ?? '',
            'file_url' => $fileUrl !== '' ? \App\Models\VitrineBlock::resolveImageAbsoluteUrl($fileUrl) : '',
            'file_name' => $doc['file_name'] ?? '',
            'has_new_file' => false,
            'cover_image_url' => $coverUrl !== '' ? \App\Models\VitrineBlock::resolveImageAbsoluteUrl($coverUrl) : '',
            'cover_image_source_type' => $coverSourceType,
            'cover_preview_url' => null,
        ];
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, documents: true },
        documents: @js($documents),
        categories: @js($categories),
        addDocument() {
            this.documents.push({
                title: '',
                category: 'catalogue',
                description: '',
                file_url: '',
                file_name: '',
                has_new_file: false,
                cover_image_url: '',
                cover_image_source_type: 'url',
                cover_preview_url: null,
            });
        },
        onPdfChange(event, doc) {
            const file = event.target.files?.[0];
            if (!file) return;
            doc.file_name = file.name;
            doc.has_new_file = true;
        },
        docCoverImagePreview(doc) {
            return doc.cover_preview_url || doc.cover_image_url || '';
        },
        onCoverImageFileChange(event, doc) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (doc.cover_preview_url?.startsWith('blob:')) {
                URL.revokeObjectURL(doc.cover_preview_url);
            }
            doc.cover_preview_url = URL.createObjectURL(file);
        },
     }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'header',
            'title' => 'En-tête',
            'subtitle' => 'Titre et introduction de la section LDM Academy',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-sky-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
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
            'section' => 'documents',
            'title' => 'Documents PDF',
            'subtitle' => 'Catalogues, guides, protocoles et notices téléchargeables',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.documents" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="documents.length === 0">
                    <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <svg class="w-10 h-10 mx-auto text-secondary/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm text-secondary font-medium">Aucun document — ajoutez des PDF pour les praticiens</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="documents.length > 0">
                    <template x-for="(doc, index) in documents" :key="'academy-doc-' + index">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 text-xs font-bold flex-shrink-0" x-text="index + 1"></span>
                                    <span class="text-xs font-bold text-primary uppercase tracking-wide truncate" x-text="doc.title || ('Document ' + (index + 1))"></span>
                                </div>
                                <button type="button" @click="documents.splice(index, 1)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4 sm:p-5 space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                        <input type="text"
                                               :name="'content[documents][' + index + '][title]'"
                                               x-model="doc.title"
                                               placeholder="Catalogue prothèses 2026"
                                               class="input-field w-full text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Catégorie</label>
                                        <select :name="'content[documents][' + index + '][category]'"
                                                x-model="doc.category"
                                                class="input-field w-full text-sm">
                                            <template x-for="(label, value) in categories" :key="value">
                                                <option :value="value" x-text="label"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description (optionnel)</label>
                                    <textarea :name="'content[documents][' + index + '][description]'"
                                              x-model="doc.description"
                                              rows="2"
                                              placeholder="Courte description du document…"
                                              class="input-field w-full text-sm resize-y min-h-[64px]"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Image de fond (carte vitrine)</label>
                                    <p class="mb-3 text-xs text-secondary">Optionnel. Si vide, la 1<sup>re</sup> page du PDF sera utilisée automatiquement comme arrière-plan.</p>
                                    @include('admin.vitrine.partials.academy-cover-image-config-fields')
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Fichier PDF</label>
                                    <input type="hidden" :name="'content[documents][' + index + '][file_url]'" :value="doc.file_url">
                                    <input type="hidden" :name="'content[documents][' + index + '][file_name]'" :value="doc.file_name">
                                    <label class="flex flex-col items-center justify-center w-full min-h-[96px] px-4 py-5 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer transition-all">
                                        <input type="file"
                                               :name="'academy_pdf_uploads[' + index + ']'"
                                               accept="application/pdf,.pdf"
                                               class="sr-only"
                                               @change="onPdfChange($event, doc)">
                                        <svg class="w-8 h-8 text-secondary/50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-primary">Cliquez pour choisir un PDF</span>
                                        <span class="text-xs text-secondary mt-1">PDF uniquement — max 20 Mo</span>
                                    </label>
                                    <p class="mt-2 text-xs text-secondary break-all" x-show="doc.file_url && !doc.has_new_file">
                                        Fichier actuel :
                                        <a :href="doc.file_url" target="_blank" rel="noopener" class="text-primary underline" x-text="doc.file_name || 'Voir le PDF'"></a>
                                    </p>
                                    <p class="mt-2 text-xs text-emerald-600 font-medium" x-show="doc.has_new_file">
                                        Nouveau fichier sélectionné (<span x-text="doc.file_name"></span>) — enregistrez pour publier
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter un document PDF', 'click' => 'addDocument()'])
                </div>
            </div>
        </div>
    </section>
</div>
