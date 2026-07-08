@php
    use App\Models\VitrineBlock;

    $c = $content;
    $categoryList = VitrineBlock::academyCategoriesList($c);
    $defaultCategoryKey = $categoryList[0]['key'] ?? 'catalogue';
    $documents = collect($c['documents'] ?? [])->map(function ($doc) use ($categoryList) {
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
            'category' => VitrineBlock::resolveAcademyDocumentCategory($doc['category'] ?? null, $categoryList),
            'description' => $doc['description'] ?? '',
            'file_type' => VitrineBlock::normalizeAcademyFileType($doc['file_type'] ?? 'pdf'),
            'file_source_type' => ($doc['file_source_type'] ?? 'upload') === 'url' ? 'url' : 'upload',
            'file_url' => $fileUrl !== '' ? VitrineBlock::resolveImageAbsoluteUrl($fileUrl) : '',
            'file_name' => $doc['file_name'] ?? '',
            'has_new_file' => false,
            'cover_image_url' => $coverUrl !== '' ? VitrineBlock::resolveImageAbsoluteUrl($coverUrl) : '',
            'cover_image_source_type' => $coverSourceType,
            'cover_preview_url' => null,
        ];
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, categories: true, documents: true },
        categoryList: @js($categoryList),
        documents: @js($documents),
        defaultCategoryKey: @js($defaultCategoryKey),
        addCategory() {
            this.categoryList.push({
                key: '',
                label: '',
                icon: '',
            });
        },
        addDocument() {
            this.documents.push({
                title: '',
                category: this.categoryList[0]?.key || this.defaultCategoryKey,
                description: '',
                file_type: 'pdf',
                file_source_type: 'upload',
                file_url: '',
                file_name: '',
                has_new_file: false,
                cover_image_url: '',
                cover_image_source_type: 'url',
                cover_preview_url: null,
            });
        },
        onFileTypeChange(doc) {
            if (doc.file_type !== 'video') {
                doc.file_source_type = 'upload';
            }
        },
        fileTypeLabel(type) {
            return ({ pdf: 'PDF', image: 'Image', video: 'Vidéo', word: 'Word' })[type] || 'PDF';
        },
        fileTypeAccept(type) {
            return ({
                pdf: 'application/pdf,.pdf',
                image: 'image/jpeg,image/jpg,image/png,image/webp,image/gif',
                video: 'video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov',
                word: '.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            })[type] || 'application/pdf,.pdf';
        },
        fileTypeUploadHint(type) {
            return ({
                pdf: 'PDF uniquement — max 20 Mo',
                image: 'JPEG, PNG, WebP, GIF — max 10 Mo',
                video: 'MP4, WebM, MOV — max 100 Mo',
                word: 'DOC, DOCX — max 20 Mo',
            })[type] || 'PDF uniquement — max 20 Mo';
        },
        coverHelpText(doc) {
            if (doc.file_type === 'pdf') {
                return 'Optionnel. Si vide, la 1re page du PDF sera utilisée automatiquement comme arrière-plan.';
            }
            return 'Optionnel. Image affichée en arrière-plan de la carte sur le site.';
        },
        onFileChange(event, doc) {
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
            'section' => 'categories',
            'title' => 'Catégories',
            'subtitle' => 'Types de documents utilisés pour classer les ressources',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.categories" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <p class="mb-4 text-sm text-secondary">
                    Définissez les catégories disponibles dans le menu déroulant de chaque ressource.
                </p>

                <template x-if="categoryList.length === 0">
                    <div class="text-center py-8 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune catégorie — ajoutez au moins une catégorie</p>
                    </div>
                </template>

                <div class="space-y-3" x-show="categoryList.length > 0">
                    <template x-for="(cat, index) in categoryList" :key="'academy-cat-' + index">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 text-xs font-bold flex-shrink-0" x-text="index + 1"></span>
                                    <span class="text-xs font-bold text-primary uppercase tracking-wide truncate" x-text="cat.label || ('Catégorie ' + (index + 1))"></span>
                                </div>
                                <button type="button" @click="categoryList.splice(index, 1)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Libellé</label>
                                    <input type="text"
                                           :name="'content[categories][' + index + '][label]'"
                                           x-model="cat.label"
                                           placeholder="Catalogues"
                                           class="input-field w-full text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Identifiant</label>
                                    <input type="text"
                                           :name="'content[categories][' + index + '][key]'"
                                           x-model="cat.key"
                                           placeholder="catalogue"
                                           class="input-field w-full text-sm">
                                    <p class="mt-1 text-xs text-secondary">Généré automatiquement si vide</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Icône Font Awesome</label>
                                    <input type="text"
                                           :name="'content[categories][' + index + '][icon]'"
                                           x-model="cat.icon"
                                           placeholder="fas fa-book-open"
                                           class="input-field w-full text-sm">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une catégorie', 'click' => 'addCategory()'])
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'documents',
            'title' => 'Documents & ressources',
            'subtitle' => 'PDF, images, vidéos et documents Word pour les praticiens',
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
                        <p class="text-sm text-secondary font-medium">Aucune ressource — ajoutez des fichiers pour les praticiens</p>
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
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                        <input type="text"
                                               :name="'content[documents][' + index + '][title]'"
                                               x-model="doc.title"
                                               placeholder="Catalogue prothèses 2026"
                                               class="input-field w-full text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Type de fichier</label>
                                        <select x-model="doc.file_type"
                                                @change="onFileTypeChange(doc)"
                                                class="input-field w-full text-sm">
                                            <option value="pdf">PDF (upload)</option>
                                            <option value="image">Image (upload)</option>
                                            <option value="video">Vidéo (URL ou upload)</option>
                                            <option value="word">Word (upload)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Catégorie</label>
                                        <input type="hidden"
                                               :name="'content[documents][' + index + '][category]'"
                                               x-model="doc.category">
                                        <select :value="doc.category"
                                                @change="doc.category = $event.target.value"
                                                x-init="$nextTick(() => { $el.value = doc.category })"
                                                class="input-field w-full text-sm">
                                            @foreach($categoryList as $cat)
                                                <option value="{{ $cat['key'] }}">{{ $cat['label'] }}</option>
                                            @endforeach
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
                                <div x-show="doc.file_type !== 'image'" x-cloak>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Image de fond (carte vitrine)</label>
                                    <p class="mb-3 text-xs text-secondary" x-text="coverHelpText(doc)"></p>
                                    @include('admin.vitrine.partials.academy-cover-image-config-fields')
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Fichier</label>
                                    @include('admin.vitrine.partials.academy-file-config-fields')
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une ressource', 'click' => 'addDocument()'])
                </div>
            </div>
        </div>
    </section>
</div>
