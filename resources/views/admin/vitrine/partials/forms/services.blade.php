@php
    $c = $content;
    $serviceItems = collect($c['items'] ?? [])->map(function ($item) {
        $imageUrl = trim((string) ($item['image_url'] ?? $item['icon_url'] ?? ''));
        $sourceType = $item['image_source_type'] ?? $item['icon_source_type'] ?? null;

        if (! $sourceType) {
            $sourceType = $imageUrl !== ''
                ? (str_contains($imageUrl, '/storage/vitrine/services') ? 'upload' : 'url')
                : 'url';
        }

        $sections = collect($item['sections'] ?? [])->map(function ($section) {
            return [
                'title' => $section['title'] ?? '',
                'description' => $section['description'] ?? '',
                'open' => true,
                'photos' => collect($section['photos'] ?? [])->map(function ($photo) {
                    $photoUrl = trim((string) ($photo['image_url'] ?? ''));

                    return [
                        'title' => $photo['title'] ?? '',
                        'image_url' => $photoUrl !== '' ? \App\Models\VitrineBlock::resolveImageAbsoluteUrl($photoUrl) : '',
                        'source_type' => ($photo['source_type'] ?? null) === 'upload' ? 'upload' : 'url',
                    ];
                })->values()->all(),
                'pendingPreviews' => [],
                'photoDropActive' => false,
            ];
        })->values()->all();

        return [
            'title' => $item['title'] ?? '',
            'slug' => $item['slug'] ?? '',
            'description' => $item['description'] ?? '',
            'content_html' => $item['content_html'] ?? '',
            'image_url' => $imageUrl !== '' ? \App\Models\VitrineBlock::resolveImageAbsoluteUrl($imageUrl) : '',
            'image_source_type' => $sourceType,
            'is_active' => filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'sections' => $sections,
            'open' => true,
            'preview_url' => null,
        ];
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, items: true },
        items: @js($serviceItems),
        itemImagePreview(item) {
            return item.preview_url || item.image_url || '';
        },
        onItemImageFileChange(event, item) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (item.preview_url?.startsWith('blob:')) {
                URL.revokeObjectURL(item.preview_url);
            }
            item.preview_url = URL.createObjectURL(file);
        },
        sectionPhotoPreview(photo) {
            return photo.preview_url || photo.image_url || '';
        },
        sectionPhotosCount(section) {
            return (section.photos?.length || 0) + (section.pendingPreviews?.length || 0);
        },
        isSectionPhotoFile(file) {
            return !!file && String(file.type || '').startsWith('image/');
        },
        addSectionPhotosFromFiles(section, inputId, files) {
            const imageFiles = Array.from(files || []).filter((file) => this.isSectionPhotoFile(file));
            if (!imageFiles.length) return;

            if (!section.pendingPreviews) {
                section.pendingPreviews = [];
            }

            const isSameFile = (left, right) => (
                left.name === right.name
                && left.size === right.size
                && left.lastModified === right.lastModified
            );

            imageFiles.forEach((file) => {
                const alreadyPending = section.pendingPreviews.some((pending) => isSameFile(pending.file, file));

                if (!alreadyPending) {
                    section.pendingPreviews.push({
                        file,
                        name: file.name,
                        preview_url: URL.createObjectURL(file),
                    });
                }
            });

            const input = document.getElementById(inputId);
            if (!input) return;

            const dataTransfer = new DataTransfer();
            section.pendingPreviews.forEach((pending) => {
                if (pending.file) {
                    dataTransfer.items.add(pending.file);
                }
            });
            input.files = dataTransfer.files;
        },
        onSectionPhotosMultipleChange(event, section) {
            this.addSectionPhotosFromFiles(section, event.target.id, Array.from(event.target.files || []));
        },
        onSectionPhotosDragOver(event, section) {
            event.preventDefault();
            section.photoDropActive = true;
        },
        onSectionPhotosDragLeave(event, section) {
            event.preventDefault();
            if (!event.currentTarget.contains(event.relatedTarget)) {
                section.photoDropActive = false;
            }
        },
        onSectionPhotosDrop(event, section, inputId) {
            event.preventDefault();
            section.photoDropActive = false;
            this.addSectionPhotosFromFiles(section, inputId, Array.from(event.dataTransfer?.files || []));
        },
        removeSavedSectionPhoto(section, photoIndex) {
            section.photos.splice(photoIndex, 1);
        },
        removePendingSectionPhoto(section, pendingIndex, inputId) {
            if (!section.pendingPreviews) return;

            const pending = section.pendingPreviews[pendingIndex];
            if (pending?.preview_url?.startsWith('blob:')) {
                URL.revokeObjectURL(pending.preview_url);
            }

            section.pendingPreviews.splice(pendingIndex, 1);

            const input = document.getElementById(inputId);
            if (!input) return;

            const dataTransfer = new DataTransfer();
            section.pendingPreviews.forEach((item) => {
                if (item.file) {
                    dataTransfer.items.add(item.file);
                }
            });
            input.files = dataTransfer.files;
        },
        newServiceItem() {
            this.items.push({
                title: '',
                slug: '',
                description: '',
                content_html: '',
                image_url: '',
                image_source_type: 'url',
                is_active: true,
                sections: [],
                open: true,
                preview_url: null,
            });
            const newIndex = this.items.length - 1;
            this.$nextTick(() => this.initServiceHtmlEditor(newIndex));
        },
        toggleItemOpen(index) {
            this.items[index].open = !this.items[index].open;
            if (this.items[index].open) {
                this.$nextTick(() => {
                    this.initServiceHtmlEditor(index);
                    this.initServiceSectionHtmlEditors(index);
                });
            }
        },
        itemLabel(item, index) {
            const title = (item.title || '').trim();
            return title !== '' ? title : 'Service ' + (index + 1);
        },
        htmlEditors: {},
        serviceSectionEditorId(serviceIndex, sectionIndex) {
            return 'service-section-html-' + serviceIndex + '-' + sectionIndex;
        },
        initServiceHtmlEditor(index) {
            if (!this.items[index]?.open) return;
            const editorId = 'service-content-html-' + index;
            if (this.htmlEditors[editorId]) return;

            const start = async () => {
                const tinymce = await (window.__vitrineServiceHtmlEditorReady || Promise.resolve(window.tinymce));
                if (!tinymce) return;

                const existing = tinymce.get(editorId);
                if (existing) {
                    this.htmlEditors[editorId] = existing;
                    return;
                }

                const textarea = document.getElementById(editorId);
                if (!textarea || textarea.dataset.tinymceInit === '1') return;

                textarea.value = this.items[index].content_html || '';
                textarea.dataset.tinymceInit = '1';

                tinymce.init({
                    ...window.__vitrineServiceHtmlEditorConfig,
                    selector: '#' + editorId,
                    setup: (editor) => {
                        editor.on('change input undo redo SetContent', () => {
                            if (this.items[index]) {
                                this.items[index].content_html = editor.getContent();
                            }
                        });
                    },
                    init_instance_callback: (editor) => {
                        this.htmlEditors[editorId] = editor;
                    },
                });
            };

            this.$nextTick(() => start());
        },
        initServiceSectionHtmlEditors(serviceIndex) {
            const item = this.items[serviceIndex];
            if (!item?.open || !item.sections) return;

            item.sections.forEach((section, sectionIndex) => {
                if (section.open) {
                    this.initServiceSectionHtmlEditor(serviceIndex, sectionIndex);
                }
            });
        },
        initServiceSectionHtmlEditor(serviceIndex, sectionIndex) {
            const item = this.items[serviceIndex];
            const section = item?.sections?.[sectionIndex];
            if (!item?.open || !section?.open) return;

            const editorId = this.serviceSectionEditorId(serviceIndex, sectionIndex);
            if (this.htmlEditors[editorId]) return;

            const start = async () => {
                const tinymce = await (window.__vitrineServiceHtmlEditorReady || Promise.resolve(window.tinymce));
                if (!tinymce) return;

                const existing = tinymce.get(editorId);
                if (existing) {
                    this.htmlEditors[editorId] = existing;
                    return;
                }

                const textarea = document.getElementById(editorId);
                if (!textarea || textarea.dataset.tinymceInit === '1') return;

                textarea.value = section.description || '';
                textarea.dataset.tinymceInit = '1';

                tinymce.init({
                    ...window.__vitrineServiceHtmlEditorConfig,
                    selector: '#' + editorId,
                    height: 280,
                    min_height: 220,
                    setup: (editor) => {
                        editor.on('change input undo redo SetContent', () => {
                            const current = this.items[serviceIndex]?.sections?.[sectionIndex];
                            if (current) {
                                current.description = editor.getContent();
                            }
                        });
                    },
                    init_instance_callback: (editor) => {
                        this.htmlEditors[editorId] = editor;
                    },
                });
            };

            this.$nextTick(() => start());
        },
        destroyServiceSectionHtmlEditors(serviceIndex = null) {
            if (!window.tinymce) return;

            Object.keys(this.htmlEditors).forEach((editorId) => {
                const isSectionEditor = editorId.startsWith('service-section-html-');
                if (!isSectionEditor) return;

                if (serviceIndex !== null && !editorId.startsWith('service-section-html-' + serviceIndex + '-')) {
                    return;
                }

                const editor = window.tinymce.get(editorId);
                if (editor) {
                    editor.remove();
                }
                delete this.htmlEditors[editorId];
            });

            document.querySelectorAll('.service-section-html-textarea').forEach((textarea) => {
                if (serviceIndex !== null) {
                    const prefix = 'service-section-html-' + serviceIndex + '-';
                    if (!textarea.id?.startsWith(prefix)) return;
                }
                delete textarea.dataset.tinymceInit;
            });
        },
        destroyAllServiceHtmlEditors() {
            if (!window.tinymce) return;
            Object.keys(this.htmlEditors).forEach((editorId) => {
                const editor = window.tinymce.get(editorId);
                if (editor) {
                    editor.remove();
                }
            });
            document.querySelectorAll('.service-content-html-textarea, .service-section-html-textarea').forEach((textarea) => {
                delete textarea.dataset.tinymceInit;
            });
            this.htmlEditors = {};
        },
        toggleServiceSectionOpen(serviceIndex, sectionIndex) {
            const section = this.items[serviceIndex]?.sections?.[sectionIndex];
            if (!section) return;
            section.open = !section.open;
            if (section.open) {
                this.$nextTick(() => this.initServiceSectionHtmlEditor(serviceIndex, sectionIndex));
            }
        },
        addServiceSection(serviceIndex) {
            const item = this.items[serviceIndex];
            if (!item) return;
            if (!item.sections) item.sections = [];
            item.sections.push({
                title: '',
                description: '',
                open: true,
                photos: [],
                pendingPreviews: [],
                photoDropActive: false,
            });
            const sectionIndex = item.sections.length - 1;
            this.$nextTick(() => this.initServiceSectionHtmlEditor(serviceIndex, sectionIndex));
        },
        removeServiceSection(serviceIndex, sectionIndex) {
            const item = this.items[serviceIndex];
            if (!item?.sections?.[sectionIndex]) return;

            const section = item.sections[sectionIndex];
            if (section?.pendingPreviews) {
                section.pendingPreviews.forEach((pending) => {
                    if (pending.preview_url?.startsWith('blob:')) {
                        URL.revokeObjectURL(pending.preview_url);
                    }
                });
            }

            this.destroyServiceSectionHtmlEditors(serviceIndex);
            item.sections.splice(sectionIndex, 1);
            this.$nextTick(() => this.initServiceSectionHtmlEditors(serviceIndex));
        },
        removeServiceItem(index) {
            this.destroyAllServiceHtmlEditors();
            this.items.splice(index, 1);
            this.$nextTick(() => {
                this.items.forEach((item, itemIndex) => {
                    if (item.open) {
                        this.initServiceHtmlEditor(itemIndex);
                        this.initServiceSectionHtmlEditors(itemIndex);
                    }
                });
            });
        },
        syncServiceHtmlEditors() {
            Object.keys(this.htmlEditors).forEach((editorId) => {
                const editor = this.htmlEditors[editorId];
                if (!editor) return;

                if (editorId.startsWith('service-content-html-')) {
                    const index = Number(editorId.replace('service-content-html-', ''));
                    if (this.items[index]) {
                        this.items[index].content_html = editor.getContent();
                    }
                    return;
                }

                const match = editorId.match(/^service-section-html-(\d+)-(\d+)$/);
                if (!match) return;
                const serviceIndex = Number(match[1]);
                const sectionIndex = Number(match[2]);
                const section = this.items[serviceIndex]?.sections?.[sectionIndex];
                if (section) {
                    section.description = editor.getContent();
                }
            });
        },
     }"
     @vitrine-services-tab-open.window="$nextTick(() => {
        items.forEach((item, itemIndex) => {
            if (item.open) {
                initServiceHtmlEditor(itemIndex);
                initServiceSectionHtmlEditors(itemIndex);
            }
        });
     })"
     @submit.document="if ($event.target.closest('form')?.contains($el)) syncServiceHtmlEditors()">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'header',
            'title' => 'En-tête',
            'subtitle' => 'Titre affiché sur la page Services (hexagones)',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-emerald-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
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
            'title' => 'Services',
            'subtitle' => 'Activez, développez et configurez chaque prestation',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.items" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="items.length === 0">
                    <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucun service configuré</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="items.length > 0">
                    <template x-for="(item, index) in items" :key="'service-' + index">
                        <div class="rounded-xl border bg-card shadow-sm overflow-hidden transition-colors"
                             :class="item.is_active ? 'border-border' : 'border-amber-200/80 bg-amber-50/20'">
                            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <button type="button"
                                        @click="toggleItemOpen(index)"
                                        class="flex items-center gap-3 min-w-0 flex-1 text-left group">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary text-xs font-bold shrink-0" x-text="index + 1"></span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-primary truncate" x-text="itemLabel(item, index)"></p>
                                        <p class="text-xs text-secondary mt-0.5" x-text="item.is_active ? 'Visible sur le site' : 'Masqué sur le site'"></p>
                                    </div>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-border/70 bg-card text-secondary shrink-0 transition-all duration-200 group-hover:border-primary/30 group-hover:text-primary"
                                          :class="item.open ? 'rotate-180 bg-primary/5 border-primary/20 text-primary' : ''">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </span>
                                </button>

                                <button type="button" @click="removeServiceItem(index)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors shrink-0"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <div x-show="item.open" x-cloak x-transition.opacity.duration.200ms class="p-4 sm:p-5 space-y-4 border-t border-border/40">
                                <label class="inline-flex items-center gap-3 cursor-pointer group pb-4 mb-4 border-b border-border/50 w-full">
                                    <input type="hidden" :name="'content[items][' + index + '][is_active]'" value="0">
                                    <input type="checkbox"
                                           :name="'content[items][' + index + '][is_active]'" value="1"
                                           x-model="item.is_active"
                                           class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                                    <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher ce service sur le site</span>
                                </label>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                        <input type="text" :name="'content[items][' + index + '][title]'" x-model="item.title" placeholder="Numérique / CFAO" class="input-field w-full text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Slug URL (optionnel)</label>
                                        <input type="text" :name="'content[items][' + index + '][slug]'" x-model="item.slug" placeholder="numerique-cfao" class="input-field w-full text-sm font-mono">
                                        <p class="mt-1 text-xs text-secondary">Généré automatiquement depuis le titre si vide.</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Image du service</label>
                                    @include('admin.vitrine.partials.service-image-config-fields')
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Résumé court (optionnel)</label>
                                    <textarea :name="'content[items][' + index + '][description]'" x-model="item.description" rows="2" placeholder="Texte d'introduction affiché dans le bandeau de la page détail…" class="input-field w-full text-sm resize-y min-h-[64px]"></textarea>
                                </div>

                                @include('admin.vitrine.partials.service-content-html-editor')

                                @include('admin.vitrine.partials.service-sections-fields')
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter un service', 'click' => 'newServiceItem()'])
                </div>
            </div>
        </div>
    </section>
</div>
