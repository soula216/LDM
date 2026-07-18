@php
    use App\Models\VitrineBlock;

    $c = $content;
    $categoryList = VitrineBlock::vosPatientsCategoriesList($c);
    $defaultCategoryKey = $categoryList[0]['key'] ?? 'esthetique';
    $videos = collect($c['videos'] ?? [])->map(function ($item) use ($categoryList) {
        $videoUrl = trim((string) ($item['video_url'] ?? ''));
        $sourceType = ($item['source_type'] ?? null) ?: (
            str_contains($videoUrl, '/storage/vitrine/vos-patients') ? 'upload' : 'url'
        );

        return [
            'title' => $item['title'] ?? '',
            'description' => $item['description'] ?? '',
            'category' => VitrineBlock::resolveVosPatientsVideoCategory($item['category'] ?? null, $categoryList),
            'video_url' => $videoUrl !== '' && $sourceType === 'upload'
                ? VitrineBlock::resolveImageAbsoluteUrl($videoUrl)
                : $videoUrl,
            'source_type' => $sourceType === 'upload' ? 'upload' : 'url',
            'file_name' => $item['file_name'] ?? '',
            'has_new_file' => false,
            'is_active' => filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ];
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, categories: true, videos: true },
        categoryList: @js($categoryList),
        videos: @js($videos),
        defaultCategoryKey: @js($defaultCategoryKey),
        addCategory() {
            this.categoryList.push({ key: '', label: '', icon: '' });
        },
        addVideo() {
            this.videos.push({
                title: '',
                description: '',
                category: this.categoryList[0]?.key || this.defaultCategoryKey,
                video_url: '',
                source_type: 'url',
                file_name: '',
                has_new_file: false,
                is_active: true,
            });
        },
        onVideoFileChange(event, video) {
            const file = event.target.files?.[0];
            if (!file) return;
            video.file_name = file.name;
            video.has_new_file = true;
        },
     }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'header',
            'title' => 'En-tête',
            'subtitle' => 'Titre et introduction de la page Vos patients',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-teal-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-teal-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
            'subtitle' => 'Classifiez les vidéos par thématique (esthétique, implantologie…)',
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
                    Définissez les catégories disponibles dans le menu déroulant de chaque vidéo.
                </p>

                <template x-if="categoryList.length === 0">
                    <div class="text-center py-8 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune catégorie — ajoutez au moins une catégorie</p>
                    </div>
                </template>

                <div class="space-y-3" x-show="categoryList.length > 0">
                    <template x-for="(cat, index) in categoryList" :key="'vos-patients-cat-' + index">
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
                                           placeholder="Esthétique"
                                           class="input-field w-full text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Identifiant</label>
                                    <input type="text"
                                           :name="'content[categories][' + index + '][key]'"
                                           x-model="cat.key"
                                           placeholder="esthetique"
                                           class="input-field w-full text-sm">
                                    <p class="mt-1 text-xs text-secondary">Généré automatiquement si vide</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Icône Font Awesome</label>
                                    <input type="text"
                                           :name="'content[categories][' + index + '][icon]'"
                                           x-model="cat.icon"
                                           placeholder="fas fa-smile"
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
            'section' => 'videos',
            'title' => 'Vidéos',
            'subtitle' => 'Titre, description courte, catégorie et lien / fichier vidéo',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-teal-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.videos" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="videos.length === 0">
                    <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune vidéo — ajoutez des cas patients à afficher</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="videos.length > 0">
                    <template x-for="(video, index) in videos" :key="'vos-patients-video-' + index">
                        <div class="rounded-xl border bg-card shadow-sm overflow-hidden transition-colors"
                             :class="video.is_active ? 'border-border' : 'border-amber-200/80 bg-amber-50/20'">
                            <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 text-xs font-bold flex-shrink-0" x-text="index + 1"></span>
                                    <span class="text-xs font-bold text-primary uppercase tracking-wide truncate" x-text="video.title || ('Vidéo ' + (index + 1))"></span>
                                </div>
                                <button type="button" @click="videos.splice(index, 1)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4 sm:p-5 space-y-4">
                                <label class="inline-flex items-center gap-3 cursor-pointer group w-full">
                                    <input type="hidden" :name="'content[videos][' + index + '][is_active]'" value="0">
                                    <input type="checkbox"
                                           :name="'content[videos][' + index + '][is_active]'" value="1"
                                           x-model="video.is_active"
                                           class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                                    <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher sur le site</span>
                                </label>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                        <input type="text"
                                               :name="'content[videos][' + index + '][title]'"
                                               x-model="video.title"
                                               placeholder="Réhabilitation esthétique complète"
                                               class="input-field w-full text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Catégorie</label>
                                        <input type="hidden"
                                               :name="'content[videos][' + index + '][category]'"
                                               x-model="video.category">
                                        <select :value="video.category"
                                                @change="video.category = $event.target.value"
                                                class="input-field w-full text-sm">
                                            <template x-for="cat in categoryList" :key="'opt-' + index + '-' + cat.key">
                                                <option :value="cat.key" x-text="cat.label" :selected="video.category === cat.key"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description courte</label>
                                    <textarea :name="'content[videos][' + index + '][description]'"
                                              x-model="video.description"
                                              rows="3"
                                              placeholder="Courte présentation du cas clinique…"
                                              class="input-field w-full text-sm resize-y"></textarea>
                                </div>

                                <input type="hidden" :name="'content[videos][' + index + '][source_type]'" x-model="video.source_type">

                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="video.source_type = 'url'"
                                            :class="video.source_type === 'url' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                        URL (YouTube / Vimeo)
                                    </button>
                                    <button type="button" @click="video.source_type = 'upload'"
                                            :class="video.source_type === 'upload' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
                                        Upload
                                    </button>
                                </div>

                                <template x-if="video.source_type === 'url'">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">URL de la vidéo</label>
                                        <input type="text"
                                               :name="'content[videos][' + index + '][video_url]'"
                                               x-model="video.video_url"
                                               placeholder="https://www.youtube.com/watch?v=… ou https://vimeo.com/…"
                                               class="input-field w-full text-sm font-mono">
                                    </div>
                                </template>

                                <template x-if="video.source_type === 'upload'">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Fichier vidéo</label>
                                        <input type="hidden" :name="'content[videos][' + index + '][video_url]'" :value="video.video_url">
                                        <label class="flex flex-col items-center justify-center w-full min-h-[88px] px-4 py-4 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer transition-all">
                                            <input type="file"
                                                   :name="'vos_patients_uploads[' + index + ']'"
                                                   accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov"
                                                   class="sr-only"
                                                   @change="onVideoFileChange($event, video)">
                                            <span class="text-sm font-medium text-primary" x-text="video.has_new_file || video.file_name ? (video.file_name || 'Fichier sélectionné') : 'Cliquez pour choisir une vidéo'"></span>
                                            <span class="text-xs text-secondary mt-1">MP4, WebM, MOV — max 100 Mo</span>
                                            <template x-if="!video.has_new_file && video.video_url">
                                                <span class="text-xs text-emerald-600 mt-2 font-medium">Vidéo déjà enregistrée</span>
                                            </template>
                                        </label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une vidéo', 'click' => 'addVideo()'])
                </div>
            </div>
        </div>
    </section>
</div>
