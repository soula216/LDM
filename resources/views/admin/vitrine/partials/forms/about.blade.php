@php
    use App\Models\VitrineBlock;

    $c = $content;
    $photos = collect($c['photos'] ?? [])->map(function ($photo) {
        return [
            'image_url' => filled($photo['image_url'] ?? null) ? VitrineBlock::resolveImageAbsoluteUrl($photo['image_url']) : '',
            'source_type' => ($photo['source_type'] ?? null) === 'upload' ? 'upload' : 'url',
            'title' => $photo['title'] ?? '',
            'caption' => $photo['caption'] ?? '',
            'preview_url' => null,
        ];
    })->values()->all();

    $videos = collect($c['videos'] ?? [])->map(function ($video) {
        return [
            'title' => $video['title'] ?? '',
            'description' => $video['description'] ?? '',
            'source_type' => ($video['source_type'] ?? null) === 'upload' ? 'upload' : 'url',
            'video_url' => filled($video['video_url'] ?? null) ? VitrineBlock::resolveImageAbsoluteUrl($video['video_url']) : '',
            'poster_source_type' => ($video['poster_source_type'] ?? null) === 'upload' ? 'upload' : 'url',
            'poster_url' => filled($video['poster_url'] ?? null) ? VitrineBlock::resolveImageAbsoluteUrl($video['poster_url']) : '',
            'poster_preview_url' => null,
        ];
    })->values()->all();

    $sections = collect($c['sections'] ?? [])->map(function ($section) {
        return [
            'title' => $section['title'] ?? '',
            'description' => $section['description'] ?? '',
        ];
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { content: true, sections: true, photos: true, videos: true },
        photos: @js($photos),
        videos: @js($videos),
        sections: @js($sections),
        addPhoto() {
            this.photos.push({ image_url: '', source_type: 'url', title: '', caption: '', preview_url: null });
        },
        addVideo() {
            this.videos.push({
                title: '',
                description: '',
                source_type: 'url',
                video_url: '',
                poster_source_type: 'url',
                poster_url: '',
                poster_preview_url: null,
            });
        },
        addSection() {
            this.sections.push({ title: '', description: '' });
        },
        photoPreview(photo) { return photo.preview_url || photo.image_url || ''; },
        youtubeVideoId(url) {
            const match = String(url || '').match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([\w-]{11})/i);
            return match ? match[1] : null;
        },
        youtubeThumbnailUrl(url) {
            const id = this.youtubeVideoId(url);
            return id ? `https://img.youtube.com/vi/${id}/hqdefault.jpg` : '';
        },
        posterPreview(video) {
            return video.poster_preview_url || video.poster_url || this.youtubeThumbnailUrl(video.video_url) || '';
        },
        usesYoutubeThumbnail(video) {
            return !video.poster_preview_url && !video.poster_url && !!this.youtubeThumbnailUrl(video.video_url);
        },
        onPhotoFileChange(event, photo) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (photo.preview_url?.startsWith('blob:')) URL.revokeObjectURL(photo.preview_url);
            photo.preview_url = URL.createObjectURL(file);
        },
        onPosterFileChange(event, video) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (video.poster_preview_url?.startsWith('blob:')) URL.revokeObjectURL(video.poster_preview_url);
            video.poster_preview_url = URL.createObjectURL(file);
        },
     }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'content',
            'title' => 'Contenu principal',
            'subtitle' => 'Titre et description de la page À propos',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-violet-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-violet-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.content" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Badge (hero)</label>
                        <input type="text" name="content[section_label]" value="{{ $c['section_label'] ?? 'À propos' }}" class="input-field w-full text-sm" placeholder="À propos">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                        <input type="text" name="content[title]" value="{{ $c['title'] ?? '' }}" class="input-field w-full text-sm" placeholder="Notre laboratoire, notre engagement">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description</label>
                    <textarea name="content[description]" rows="5" class="input-field w-full text-sm resize-y min-h-[120px]" placeholder="Présentez votre laboratoire, votre histoire et vos valeurs…">{{ $c['description'] ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'sections',
            'title' => 'Contenu secondaire',
            'subtitle' => 'Sections affichées sous la présentation sur la page publique',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-emerald-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.sections" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-6">
                <div class="rounded-xl border border-border bg-neutral-50/60 p-4 sm:p-5 space-y-4">
                    <div>
                        <h4 class="text-sm font-semibold text-primary">En-tête du bloc</h4>
                        <p class="text-xs text-secondary mt-1">Badge, titre et sous-titre affichés au-dessus des sections sur la page publique.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Badge</label>
                            <input type="text" name="content[sections_kicker]" value="{{ $c['sections_kicker'] ?? 'En détail' }}" class="input-field w-full text-sm" placeholder="En détail">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                            <input type="text" name="content[sections_heading]" value="{{ $c['sections_heading'] ?? '' }}" class="input-field w-full text-sm" placeholder="Nos engagements & expertises">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Sous-titre</label>
                        <textarea name="content[sections_lead]" rows="2" class="input-field w-full text-sm resize-y min-h-[72px]" placeholder="Découvrez les piliers qui structurent notre laboratoire…">{{ $c['sections_lead'] ?? '' }}</textarea>
                    </div>
                </div>

                <div>
                <p class="text-sm text-secondary mb-4">
                    Ajoutez une ou plusieurs sections. Chaque section comporte un titre et une description, affichés dans l'ordre sous le bloc « Présentation ».
                </p>

                <template x-if="sections.length === 0">
                    <div class="text-center py-8 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune section — ajoutez du contenu complémentaire (valeurs, expertise, engagements…)</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="sections.length > 0">
                    <template x-for="(section, index) in sections" :key="'about-section-' + index">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <span class="text-xs font-bold text-primary uppercase tracking-wide" x-text="section.title || ('Section ' + (index + 1))"></span>
                                <div class="flex items-center gap-1">
                                    <button type="button"
                                            @click="if (index > 0) { const item = sections.splice(index, 1)[0]; sections.splice(index - 1, 0, item); }"
                                            :disabled="index === 0"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-secondary hover:bg-neutral-100 disabled:opacity-30 disabled:pointer-events-none"
                                            title="Monter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    </button>
                                    <button type="button"
                                            @click="if (index < sections.length - 1) { const item = sections.splice(index, 1)[0]; sections.splice(index + 1, 0, item); }"
                                            :disabled="index === sections.length - 1"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-secondary hover:bg-neutral-100 disabled:opacity-30 disabled:pointer-events-none"
                                            title="Descendre">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <button type="button" @click="sections.splice(index, 1)" class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 sm:p-5 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                    <input type="text" :name="'content[sections][' + index + '][title]'" x-model="section.title" class="input-field w-full text-sm" placeholder="Ex. Notre expertise">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description</label>
                                    <textarea :name="'content[sections][' + index + '][description]'" x-model="section.description" rows="4" class="input-field w-full text-sm resize-y min-h-[100px]" placeholder="Décrivez cette section…"></textarea>
                                    <p class="mt-1.5 text-xs text-secondary">Séparez les paragraphes par une ligne vide.</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une section', 'click' => 'addSection()'])
                </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'photos',
            'title' => 'Photos',
            'subtitle' => 'Galerie visuelle de la page À propos',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-sky-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.photos" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="photos.length === 0">
                    <div class="text-center py-8 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune photo — ajoutez des visuels pour illustrer votre page</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="photos.length > 0">
                    <template x-for="(photo, index) in photos" :key="'about-photo-' + index">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <span class="text-xs font-bold text-primary uppercase tracking-wide" x-text="photo.title || ('Photo ' + (index + 1))"></span>
                                <button type="button" @click="photos.splice(index, 1)" class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            <div class="p-4 sm:p-5 space-y-4">
                                <input type="hidden" :name="'content[photos][' + index + '][source_type]'" x-model="photo.source_type">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre (optionnel)</label>
                                        <input type="text" :name="'content[photos][' + index + '][title]'" x-model="photo.title" class="input-field w-full text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Légende (optionnel)</label>
                                        <input type="text" :name="'content[photos][' + index + '][caption]'" x-model="photo.caption" class="input-field w-full text-sm">
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="photo.source_type = 'url'" :class="photo.source_type === 'url' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">URL</button>
                                    <button type="button" @click="photo.source_type = 'upload'" :class="photo.source_type === 'upload' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">Upload</button>
                                </div>
                                <template x-if="photo.source_type === 'url'">
                                    <input type="text" :name="'content[photos][' + index + '][image_url]'" x-model="photo.image_url" placeholder="https://…" class="input-field w-full text-sm font-mono">
                                </template>
                                <template x-if="photo.source_type === 'upload'">
                                    <div>
                                        <input type="hidden" :name="'content[photos][' + index + '][image_url]'" :value="photo.image_url">
                                        <label class="flex flex-col items-center justify-center w-full min-h-[88px] px-4 py-4 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer">
                                            <input type="file" :name="'about_photo_uploads[' + index + ']'" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" class="sr-only" @change="onPhotoFileChange($event, photo)">
                                            <span class="text-sm font-medium text-primary">Choisir une image</span>
                                            <span class="text-xs text-secondary mt-1">JPEG, PNG, WebP, GIF — max 10 Mo</span>
                                        </label>
                                    </div>
                                </template>
                                <div x-show="photoPreview(photo)" class="max-w-xs">
                                    <img :src="photoPreview(photo)" alt="" class="w-full rounded-xl border border-border object-cover aspect-[4/3]">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une photo', 'click' => 'addPhoto()'])
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'videos',
            'title' => 'Vidéos',
            'subtitle' => 'Vidéos YouTube, Vimeo ou fichiers uploadés',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-rose-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.videos" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="videos.length === 0">
                    <div class="text-center py-8 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune vidéo — ajoutez une présentation ou une visite du laboratoire</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="videos.length > 0">
                    <template x-for="(video, index) in videos" :key="'about-video-' + index">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <span class="text-xs font-bold text-primary uppercase tracking-wide" x-text="video.title || ('Vidéo ' + (index + 1))"></span>
                                <button type="button" @click="videos.splice(index, 1)" class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            <div class="p-4 sm:p-5 space-y-4">
                                <input type="hidden" :name="'content[videos][' + index + '][source_type]'" x-model="video.source_type">
                                <input type="hidden" :name="'content[videos][' + index + '][poster_source_type]'" x-model="video.poster_source_type">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                        <input type="text" :name="'content[videos][' + index + '][title]'" x-model="video.title" class="input-field w-full text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description (optionnel)</label>
                                        <input type="text" :name="'content[videos][' + index + '][description]'" x-model="video.description" class="input-field w-full text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Vidéo</label>
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <button type="button" @click="video.source_type = 'url'" :class="video.source_type === 'url' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">URL</button>
                                        <button type="button" @click="video.source_type = 'upload'" :class="video.source_type === 'upload' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">Upload</button>
                                    </div>
                                    <template x-if="video.source_type === 'url'">
                                        <input type="url" :name="'content[videos][' + index + '][video_url]'" x-model="video.video_url" placeholder="https://www.youtube.com/watch?v=…" class="input-field w-full text-sm font-mono">
                                    </template>
                                    <template x-if="video.source_type === 'upload'">
                                        <div>
                                            <input type="hidden" :name="'content[videos][' + index + '][video_url]'" :value="video.video_url">
                                            <label class="flex flex-col items-center justify-center w-full min-h-[88px] px-4 py-4 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer">
                                                <input type="file" :name="'about_video_uploads[' + index + ']'" accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov" class="sr-only">
                                                <span class="text-sm font-medium text-primary">Choisir une vidéo</span>
                                                <span class="text-xs text-secondary mt-1">MP4, WebM, MOV — max 100 Mo</span>
                                            </label>
                                            <p class="mt-2 text-xs text-secondary break-all" x-show="video.video_url" x-text="'Fichier actuel : ' + video.video_url"></p>
                                        </div>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Miniature (optionnel)</label>
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <button type="button" @click="video.poster_source_type = 'url'" :class="video.poster_source_type === 'url' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">URL</button>
                                        <button type="button" @click="video.poster_source_type = 'upload'" :class="video.poster_source_type === 'upload' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">Upload</button>
                                    </div>
                                    <template x-if="video.poster_source_type === 'url'">
                                        <input type="text" :name="'content[videos][' + index + '][poster_url]'" x-model="video.poster_url" placeholder="https://…" class="input-field w-full text-sm font-mono">
                                    </template>
                                    <template x-if="video.poster_source_type === 'upload'">
                                        <div>
                                            <input type="hidden" :name="'content[videos][' + index + '][poster_url]'" :value="video.poster_url">
                                            <label class="flex flex-col items-center justify-center w-full min-h-[72px] px-4 py-3 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer">
                                                <input type="file" :name="'about_poster_uploads[' + index + ']'" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" class="sr-only" @change="onPosterFileChange($event, video)">
                                                <span class="text-sm font-medium text-primary">Choisir une miniature</span>
                                            </label>
                                        </div>
                                    </template>
                                    <div x-show="posterPreview(video)" class="max-w-[200px] mt-3">
                                        <img :src="posterPreview(video)" alt="" class="w-full rounded-lg border border-border object-cover aspect-video">
                                        <p x-show="usesYoutubeThumbnail(video)" class="mt-2 text-xs text-secondary">
                                            Miniature YouTube utilisée automatiquement
                                        </p>
                                    </div>
                                </div>
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
