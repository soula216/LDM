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

    $sections = collect($c['sections'] ?? [])->values();
    $missionSection = $sections->first(function ($section) {
        return str_contains(mb_strtolower((string) ($section['title'] ?? '')), 'mission');
    }) ?? $sections->get(0) ?? [];
    $principlesSection = $sections->first(function ($section) {
        $title = mb_strtolower((string) ($section['title'] ?? ''));

        return str_contains($title, 'principe') || str_contains($title, 'valeur');
    }) ?? $sections->get(1) ?? [];

    $mission = [
        'title' => $missionSection['title'] ?? 'Notre mission',
        'description' => $missionSection['description'] ?? '',
    ];
    $principles = [
        'title' => $principlesSection['title'] ?? 'Nos principe',
        'description' => $principlesSection['description'] ?? '',
    ];

    $infoPageDefinitions = VitrineBlock::aboutInfoPageDefinitions();
    $infoPages = [];
    foreach ($infoPageDefinitions as $slug => $defaultTitle) {
        $stored = $c['info_pages'][$slug] ?? [];
        $infoPages[$slug] = [
            'title' => $stored['title'] ?? $defaultTitle,
            'content_html' => $stored['content_html'] ?? '',
            'hero_kicker' => $stored['hero_kicker'] ?? '',
            'hero_heading' => $stored['hero_heading'] ?? '',
            'hero_lead' => $stored['hero_lead'] ?? '',
        ];
    }

    $mediaPageData = $c['media_page'] ?? [];
    $mediaPagePhotos = collect($mediaPageData['photos'] ?? [])->map(function ($photo) {
        return [
            'image_url' => filled($photo['image_url'] ?? null) ? VitrineBlock::resolveImageAbsoluteUrl($photo['image_url']) : '',
            'title' => $photo['title'] ?? '',
            'description' => $photo['description'] ?? '',
            'preview_url' => null,
        ];
    })->values()->all();

    $mediaPageSlug = VitrineBlock::aboutMediaPageSlug();
    $mediaPageLabel = VitrineBlock::aboutMediaPageLabel();
    $laboratoryPageSlug = VitrineBlock::aboutLaboratoryPageSlug();
    $laboratoryPageLabel = VitrineBlock::aboutLaboratoryPageLabel();
    $processPageSlug = VitrineBlock::aboutProcessPageSlug();
    $processPageLabel = VitrineBlock::aboutProcessPageLabel();

    $subMenus = collect(VitrineBlock::orderedAboutSubPages($c))
        ->mapWithKeys(fn ($page, $slug) => [$slug => $page['label']])
        ->all();
    $subMenuItems = collect($subMenus)
        ->map(fn ($label, $slug) => ['slug' => $slug, 'label' => $label])
        ->values()
        ->all();
    $menuVisibility = collect(VitrineBlock::aboutMenuVisibilityDefaults())
        ->mapWithKeys(fn (bool $default, string $slug) => [
            $slug => filter_var($c['menu_visibility'][$slug] ?? $default, FILTER_VALIDATE_BOOLEAN),
        ])
        ->all();
    $menuVisibilityGroups = [
        VitrineBlock::aboutOverviewPageSlug() => [
            'label' => VitrineBlock::aboutOverviewPageLabel(),
            'children' => VitrineBlock::aboutOverviewTabs(),
        ],
        VitrineBlock::aboutCollaborationPageSlug() => [
            'label' => VitrineBlock::aboutCollaborationPageLabel(),
            'children' => VitrineBlock::aboutCollaborationTabs(),
        ],
        VitrineBlock::aboutWorkPageSlug() => [
            'label' => VitrineBlock::aboutWorkPageLabel(),
            'children' => VitrineBlock::aboutWorkTabs(),
        ],
        VitrineBlock::aboutLaboratoryPageSlug() => [
            'label' => VitrineBlock::aboutLaboratoryPageLabel(),
            'children' => [],
        ],
    ];

    $aboutBlock = $aboutBlock ?? null;
    $laboratoryBlock = $laboratoryBlock ?? null;
    $processBlock = $processBlock ?? null;
    $activeAboutSub = $activeAboutSub ?? 'qui-sommes-nous';
    if (! array_key_exists($activeAboutSub, $subMenus)) {
        $activeAboutSub = 'qui-sommes-nous';
    }
@endphp

<div class="vitrine-tab-form about-admin-layout"
     x-data="{
        activeSub: @js($activeAboutSub),
        subMenuItems: @js($subMenuItems),
        menuVisibility: @js($menuVisibility),
        open: { content: true, photos: true, videos: true, mediaPhotos: true },
        photos: @js($photos),
        videos: @js($videos),
        infoPages: @js($infoPages),
        mediaPagePhotos: @js($mediaPagePhotos),
        htmlEditors: {},
        moveSubMenu(index, direction) {
            const target = index + direction;
            if (target < 0 || target >= this.subMenuItems.length) return;
            const [item] = this.subMenuItems.splice(index, 1);
            this.subMenuItems.splice(target, 0, item);
        },
        addPhoto() {
            this.photos.push({ image_url: '', source_type: 'url', title: '', caption: '', preview_url: null });
        },
        addMediaPagePhoto() {
            this.mediaPagePhotos.push({ image_url: '', title: '', description: '', preview_url: null });
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
        initAboutInfoHtmlEditor(slug) {
            if (!this.infoPages[slug]) return;
            const editorId = 'about-info-html-' + slug;
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

                textarea.value = this.infoPages[slug].content_html || '';
                textarea.dataset.tinymceInit = '1';

                tinymce.init({
                    ...window.__vitrineServiceHtmlEditorConfig,
                    selector: '#' + editorId,
                    setup: (editor) => {
                        editor.on('change input undo redo SetContent', () => {
                            if (this.infoPages[slug]) {
                                this.infoPages[slug].content_html = editor.getContent();
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
     }"
     x-init="
        $watch('activeSub', (sub) => {
            if (infoPages[sub]) {
                $nextTick(() => initAboutInfoHtmlEditor(sub));
            }
            if (sub === '{{ $processPageSlug }}') {
                $nextTick(() => $dispatch('vitrine-process-tab-open'));
            }
        });
     "
     @vitrine-about-tab-open.window="
        $nextTick(() => {
            if (infoPages[activeSub]) initAboutInfoHtmlEditor(activeSub);
            if (activeSub === '{{ $processPageSlug }}') $dispatch('vitrine-process-tab-open');
        });
     ">

    <aside class="about-admin-subnav">
        <nav class="bg-card/60 backdrop-blur-md border border-border rounded-2xl shadow-sm overflow-hidden" aria-label="Sous-sections Le Laboratoire">
            <div class="px-4 py-3 border-b border-border/70">
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-secondary">Sous-menus</p>
                <p class="text-[11px] text-secondary mt-1">Gérez l’ordre et la visibilité dans le menu public.</p>
            </div>
            <form action="{{ route('admin.vitrine.about-subpage-order') }}" method="POST" class="p-1.5">
                @csrf
                @method('PATCH')
                <input type="hidden" name="active_sub" :value="activeSub">

                <div class="space-y-1">
                    <template x-for="(item, index) in subMenuItems" :key="item.slug">
                        <div
                            :class="activeSub === item.slug
                                ? 'bg-gradient-to-r from-primary to-primary/90 text-white shadow-md shadow-primary/25'
                                : 'text-secondary hover:text-primary hover:bg-neutral-100/80'"
                            class="flex items-center gap-1 rounded-xl transition-all duration-200"
                        >
                            <input type="hidden" name="subpage_order[]" :value="item.slug">
                            <button
                                type="button"
                                @click="activeSub = item.slug"
                                class="min-w-0 flex-1 px-3 py-2.5 text-sm font-semibold text-left"
                            >
                                <span class="block truncate" x-text="item.label"></span>
                            </button>
                            <div class="flex items-center gap-0.5 pr-1.5">
                                <button
                                    type="button"
                                    @click.stop="moveSubMenu(index, -1)"
                                    :disabled="index === 0"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg disabled:opacity-30 hover:bg-white/15"
                                    aria-label="Monter"
                                    title="Monter"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    @click.stop="moveSubMenu(index, 1)"
                                    :disabled="index === subMenuItems.length - 1"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg disabled:opacity-30 hover:bg-white/15"
                                    aria-label="Descendre"
                                    title="Descendre"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/70 space-y-3">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-secondary">Visibilité dans le menu</p>
                        <p class="text-[10px] text-secondary mt-1">Niveau 1 : groupes · Niveau 2 : pages</p>
                    </div>

                    @foreach($menuVisibilityGroups as $groupSlug => $group)
                        <div class="rounded-xl border border-border/70 bg-card overflow-hidden">
                            <label class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer">
                                <input type="hidden" name="menu_visibility[{{ $groupSlug }}]" value="0">
                                <input type="checkbox"
                                       name="menu_visibility[{{ $groupSlug }}]"
                                       value="1"
                                       x-model="menuVisibility['{{ $groupSlug }}']"
                                       class="rounded border-border text-primary focus:ring-primary/30">
                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs font-semibold text-primary truncate">{{ $group['label'] }}</span>
                                    <span class="block text-[10px] text-secondary">Sous-menu niveau 1</span>
                                </span>
                            </label>

                            @if(count($group['children']) > 0)
                                <div class="border-t border-border/60 bg-neutral-50/60 px-3 py-2 space-y-2"
                                     :class="menuVisibility['{{ $groupSlug }}'] ? '' : 'opacity-50'">
                                    @foreach($group['children'] as $childSlug => $child)
                                        <label class="flex items-center gap-2 pl-3 cursor-pointer">
                                            <input type="hidden" name="menu_visibility[{{ $childSlug }}]" value="0">
                                            <input type="checkbox"
                                                   name="menu_visibility[{{ $childSlug }}]"
                                                   value="1"
                                                   x-model="menuVisibility['{{ $childSlug }}']"
                                                   class="rounded border-border text-primary focus:ring-primary/30">
                                            <span class="text-[11px] font-medium text-secondary truncate">{{ $child['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn-primary w-full mt-3 px-3 py-2 text-xs font-semibold rounded-xl">
                    Enregistrer le menu
                </button>
            </form>
        </nav>
    </aside>

    <div class="about-admin-panels min-w-0 space-y-6 sm:space-y-8">
        <form action="{{ route('admin.vitrine.update', $aboutBlock) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6 sm:space-y-8"
              x-show="activeSub !== '{{ $laboratoryPageSlug }}' && activeSub !== '{{ $processPageSlug }}'"
              x-cloak>
            @csrf
            @method('PATCH')
            <input type="hidden" name="return_tab" value="about">
            <input type="hidden" name="return_sub" :value="activeSub">

        {{-- Qui sommes-nous --}}
        <div x-show="activeSub === 'qui-sommes-nous'" x-cloak class="space-y-6 sm:space-y-8">
            <section class="rounded-2xl border border-border bg-card overflow-hidden">
                @component('admin.vitrine.partials.collapsible-header', [
                    'section' => 'content',
                    'title' => 'Contenu principal',
                    'subtitle' => 'Titre et description de la page Le Laboratoire',
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
                                <input type="text" name="content[section_label]" value="{{ $c['section_label'] ?? 'Le Laboratoire' }}" class="input-field w-full text-sm" placeholder="Le Laboratoire">
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

            <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
                @component('admin.vitrine.partials.collapsible-header', [
                    'section' => 'photos',
                    'title' => 'Photos',
                    'subtitle' => 'Galerie visuelle de la page Le Laboratoire',
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

        {{-- Notre mission --}}
        <div x-show="activeSub === 'notre-mission'" x-cloak class="space-y-6">
            <section class="rounded-2xl border border-border bg-card overflow-hidden">
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-border/60 bg-gradient-to-r from-emerald-500/5 to-transparent">
                    <h4 class="text-sm sm:text-base font-bold text-primary">Notre mission</h4>
                    <p class="text-xs text-secondary mt-0.5">Contenu affiché dans le bloc secondaire de la page publique</p>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="rounded-xl border border-border bg-neutral-50/60 p-4 sm:p-5 space-y-4">
                        <div>
                            <h5 class="text-sm font-semibold text-primary">En-tête du bloc secondaire</h5>
                            <p class="text-xs text-secondary mt-1">Badge, titre et sous-titre affichés au-dessus de Notre mission et Nos principe.</p>
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
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                        <input type="text" name="content[sections][0][title]" value="{{ $mission['title'] }}" class="input-field w-full text-sm" placeholder="Notre mission">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description</label>
                        <textarea name="content[sections][0][description]" rows="8" class="input-field w-full text-sm resize-y min-h-[180px]" placeholder="Décrivez la mission du laboratoire…">{{ $mission['description'] }}</textarea>
                        <p class="mt-1.5 text-xs text-secondary">Séparez les paragraphes par une ligne vide.</p>
                    </div>
                </div>
            </section>
        </div>

        {{-- Nos principe --}}
        <div x-show="activeSub === 'nos-principe'" x-cloak class="space-y-6">
            <section class="rounded-2xl border border-border bg-card overflow-hidden">
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-border/60 bg-gradient-to-r from-amber-500/5 to-transparent">
                    <h4 class="text-sm sm:text-base font-bold text-primary">Nos principe</h4>
                    <p class="text-xs text-secondary mt-0.5">Contenu affiché dans le bloc secondaire de la page publique</p>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="rounded-xl border border-border bg-neutral-50/60 p-4 sm:p-5 space-y-4">
                        <div>
                            <h5 class="text-sm font-semibold text-primary">En-tête du bloc secondaire</h5>
                            <p class="text-xs text-secondary mt-1">Badge, titre et sous-titre affichés dans le hero de la page Nos principe.</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Badge</label>
                                <input type="text" name="content[principles_kicker]" value="{{ $c['principles_kicker'] ?? 'Nos valeurs' }}" class="input-field w-full text-sm" placeholder="Nos valeurs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                <input type="text" name="content[principles_heading]" value="{{ $c['principles_heading'] ?? '' }}" class="input-field w-full text-sm" placeholder="Les principes qui guident notre exigence">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Sous-titre</label>
                            <textarea name="content[principles_lead]" rows="2" class="input-field w-full text-sm resize-y min-h-[72px]" placeholder="Précision, qualité et confiance au cœur de chaque collaboration…">{{ $c['principles_lead'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                        <input type="text" name="content[sections][1][title]" value="{{ $principles['title'] }}" class="input-field w-full text-sm" placeholder="Nos principe">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description</label>
                        <textarea name="content[sections][1][description]" rows="8" class="input-field w-full text-sm resize-y min-h-[180px]" placeholder="Décrivez vos principes…">{{ $principles['description'] }}</textarea>
                        <p class="mt-1.5 text-xs text-secondary">Séparez les paragraphes par une ligne vide.</p>
                    </div>
                </div>
            </section>
        </div>

        @foreach($infoPageDefinitions as $slug => $defaultTitle)
            <div x-show="activeSub === '{{ $slug }}'"
                 x-cloak
                 class="space-y-6">
                @if($slug === 'garantie')
                    <section class="rounded-2xl border border-border bg-card overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-border/60 bg-gradient-to-r from-emerald-500/5 to-transparent">
                            <h4 class="text-sm sm:text-base font-bold text-primary">En-tête du bloc secondaire</h4>
                            <p class="text-xs text-secondary mt-0.5">Badge, titre et sous-titre affichés dans le hero de la page Garantie</p>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Badge</label>
                                    <input type="text"
                                           name="content[info_pages][{{ $slug }}][hero_kicker]"
                                           x-model="infoPages['{{ $slug }}'].hero_kicker"
                                           class="input-field w-full text-sm"
                                           placeholder="Votre sérénité">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                    <input type="text"
                                           name="content[info_pages][{{ $slug }}][hero_heading]"
                                           x-model="infoPages['{{ $slug }}'].hero_heading"
                                           class="input-field w-full text-sm"
                                           placeholder="Une garantie fondée sur la qualité et la confiance">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Sous-titre</label>
                                <textarea name="content[info_pages][{{ $slug }}][hero_lead]"
                                          x-model="infoPages['{{ $slug }}'].hero_lead"
                                          rows="2"
                                          class="input-field w-full text-sm resize-y min-h-[72px]"
                                          placeholder="Nous nous engageons durablement sur la qualité de nos réalisations…"></textarea>
                            </div>
                        </div>
                    </section>
                @endif

                <section class="rounded-2xl border border-border bg-card overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-border/60 bg-gradient-to-r from-sky-500/5 to-transparent">
                        <h4 class="text-sm sm:text-base font-bold text-primary">{{ $defaultTitle }}</h4>
                        <p class="text-xs text-secondary mt-0.5">Titre et contenu détaillé de la page publique</p>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                            <input type="text"
                                   name="content[info_pages][{{ $slug }}][title]"
                                   x-model="infoPages['{{ $slug }}'].title"
                                   class="input-field w-full text-sm"
                                   placeholder="{{ $defaultTitle }}">
                        </div>
                        <div class="service-html-editor-field">
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Contenu détaillé</label>
                            <textarea
                                id="about-info-html-{{ $slug }}"
                                name="content[info_pages][{{ $slug }}][content_html]"
                                class="about-info-html-textarea"
                            >{{ $infoPages[$slug]['content_html'] ?? '' }}</textarea>
                            <p class="mt-1.5 text-xs text-secondary">Éditeur visuel : titres, listes, liens, images, tableaux, etc.</p>
                        </div>
                    </div>
                </section>
            </div>
        @endforeach

        {{-- Certifications --}}
        <div x-show="activeSub === '{{ $mediaPageSlug }}'" x-cloak class="space-y-6 sm:space-y-8">
            <section class="rounded-2xl border border-border bg-card overflow-hidden">
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-border/60 bg-gradient-to-r from-emerald-500/5 to-transparent">
                    <h4 class="text-sm sm:text-base font-bold text-primary">En-tête du bloc secondaire</h4>
                    <p class="text-xs text-secondary mt-0.5">Badge, titre et sous-titre affichés dans le hero de la page {{ $mediaPageLabel }}</p>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Badge</label>
                            <input type="text"
                                   name="content[certifications_kicker]"
                                   value="{{ $c['certifications_kicker'] ?? 'Qualité certifiée' }}"
                                   class="input-field w-full text-sm"
                                   placeholder="Qualité certifiée">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                            <input type="text"
                                   name="content[certifications_heading]"
                                   value="{{ $c['certifications_heading'] ?? '' }}"
                                   class="input-field w-full text-sm"
                                   placeholder="Des standards reconnus, une qualité maîtrisée">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Sous-titre</label>
                        <textarea name="content[certifications_lead]"
                                  rows="2"
                                  class="input-field w-full text-sm resize-y min-h-[72px]"
                                  placeholder="Nos certifications témoignent de notre engagement qualité…">{{ $c['certifications_lead'] ?? '' }}</textarea>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-border bg-card overflow-hidden">
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-border/60 bg-gradient-to-r from-violet-500/5 to-transparent">
                    <h4 class="text-sm sm:text-base font-bold text-primary">Contenu principal</h4>
                    <p class="text-xs text-secondary mt-0.5">Badge, titre et description de la page {{ $mediaPageLabel }}</p>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Badge (hero)</label>
                            <input type="text"
                                   name="content[media_page][section_label]"
                                   value="{{ $mediaPageData['section_label'] ?? $mediaPageLabel }}"
                                   class="input-field w-full text-sm"
                                   placeholder="{{ $mediaPageLabel }}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                            <input type="text"
                                   name="content[media_page][title]"
                                   value="{{ $mediaPageData['title'] ?? $mediaPageLabel }}"
                                   class="input-field w-full text-sm"
                                   placeholder="{{ $mediaPageLabel }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description</label>
                        <textarea name="content[media_page][description]"
                                  rows="5"
                                  class="input-field w-full text-sm resize-y min-h-[120px]"
                                  placeholder="Présentez vos certifications et accréditations…">{{ $mediaPageData['description'] ?? '' }}</textarea>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
                @component('admin.vitrine.partials.collapsible-header', [
                    'section' => 'mediaPhotos',
                    'title' => 'Photos',
                    'subtitle' => 'Galerie photos de la page (upload uniquement)',
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

                <div x-show="open.mediaPhotos" x-cloak x-transition.opacity.duration.200ms>
                    <div class="p-4 sm:p-6">
                        <template x-if="mediaPagePhotos.length === 0">
                            <div class="text-center py-8 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                                <p class="text-sm text-secondary font-medium">Aucune photo — ajoutez des images pour illustrer la page</p>
                            </div>
                        </template>

                        <div class="space-y-4" x-show="mediaPagePhotos.length > 0">
                            <template x-for="(photo, index) in mediaPagePhotos" :key="'about-media-photo-' + index">
                                <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                                    <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                        <span class="text-xs font-bold text-primary uppercase tracking-wide" x-text="photo.title || ('Photo ' + (index + 1))"></span>
                                        <button type="button" @click="mediaPagePhotos.splice(index, 1)" class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <div class="p-4 sm:p-5 space-y-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre de la photo</label>
                                            <input type="text" :name="'content[media_page][photos][' + index + '][title]'" x-model="photo.title" class="input-field w-full text-sm" placeholder="Ex. Atelier CFAO">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description</label>
                                            <textarea :name="'content[media_page][photos][' + index + '][description]'" x-model="photo.description" rows="3" class="input-field w-full text-sm resize-y min-h-[80px]" placeholder="Décrivez cette photo…"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Image (upload)</label>
                                            <input type="hidden" :name="'content[media_page][photos][' + index + '][image_url]'" :value="photo.image_url">
                                            <label class="flex flex-col items-center justify-center w-full min-h-[88px] px-4 py-4 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer">
                                                <input type="file" :name="'about_media_photo_uploads[' + index + ']'" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" class="sr-only" @change="onPhotoFileChange($event, photo)">
                                                <span class="text-sm font-medium text-primary">Choisir une image</span>
                                                <span class="text-xs text-secondary mt-1">JPEG, PNG, WebP, GIF — max 10 Mo</span>
                                            </label>
                                        </div>
                                        <div x-show="photoPreview(photo)" class="max-w-xs">
                                            <img :src="photoPreview(photo)" alt="" class="w-full rounded-xl border border-border object-cover aspect-[4/3]">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 pt-4 border-t border-border/60">
                            @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une photo', 'click' => 'addMediaPagePhoto()'])
                        </div>
                    </div>
                </div>
            </section>
        </div>

            <div class="pt-6 border-t border-border flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <label class="inline-flex items-center gap-3 cursor-pointer group">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ ($aboutBlock->is_active ?? true) ? 'checked' : '' }}
                           class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                    <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher ce bloc sur le site</span>
                </label>
                <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/25 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Enregistrer {{ $aboutBlock->label ?? 'Le Laboratoire' }}
                </button>
            </div>
        </form>

        @if($laboratoryBlock)
            <form action="{{ route('admin.vitrine.update', $laboratoryBlock) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-6 sm:space-y-8"
                  x-show="activeSub === '{{ $laboratoryPageSlug }}'"
                  x-cloak>
                @csrf
                @method('PATCH')
                <input type="hidden" name="return_tab" value="about">
                <input type="hidden" name="return_sub" value="{{ $laboratoryPageSlug }}">

                @include('admin.vitrine.partials.forms.laboratory', ['content' => $laboratoryBlock->content ?? []])

                <div class="pt-6 border-t border-border flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                    <label class="inline-flex items-center gap-3 cursor-pointer group">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ $laboratoryBlock->is_active ? 'checked' : '' }}
                               class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                        <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher cette page sur le site</span>
                    </label>
                    <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/25 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Enregistrer {{ $laboratoryPageLabel }}
                    </button>
                </div>
            </form>
        @endif

        @if($processBlock)
            <form action="{{ route('admin.vitrine.update', $processBlock) }}"
                  method="POST"
                  class="space-y-6 sm:space-y-8"
                  x-show="activeSub === '{{ $processPageSlug }}'"
                  x-cloak>
                @csrf
                @method('PATCH')
                <input type="hidden" name="return_tab" value="about">
                <input type="hidden" name="return_sub" value="{{ $processPageSlug }}">

                @include('admin.vitrine.partials.forms.process', ['content' => $processBlock->content ?? []])

                <div class="pt-6 border-t border-border flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                    <label class="inline-flex items-center gap-3 cursor-pointer group">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ $processBlock->is_active ? 'checked' : '' }}
                               class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                        <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher cette page sur le site</span>
                    </label>
                    <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/25 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Enregistrer {{ $processPageLabel }}
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<style>
    .about-admin-layout {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: 1.5rem;
    }
    .about-admin-subnav {
        width: 15rem;
        flex-shrink: 0;
        position: sticky;
        top: 6rem;
    }
    .about-admin-panels {
        flex: 1 1 0%;
        min-width: 0;
    }
    @media (max-width: 768px) {
        .about-admin-layout {
            flex-direction: column;
        }
        .about-admin-subnav {
            width: 100%;
            position: static;
        }
    }
</style>
