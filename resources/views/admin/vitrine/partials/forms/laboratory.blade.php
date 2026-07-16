@php
    use App\Models\VitrineBlock;

    $c = $content;
    $categories = VitrineBlock::laboratoryCategories();

    $media = collect($c['media'] ?? null);
    if ($media->isEmpty()) {
        $media = collect();
        foreach ($c['photos'] ?? [] as $photo) {
            $media->push(array_merge($photo, ['type' => 'image']));
        }
        foreach ($c['videos'] ?? [] as $video) {
            $media->push(array_merge($video, ['type' => 'video']));
        }
    }

    $mediaItems = $media->values()->map(function ($item, $index) {
        $type = ($item['type'] ?? '') === 'video' ? 'video' : 'image';

        return [
            '_uid' => 'media-' . $index . '-' . uniqid(),
            'open' => false,
            'type' => $type,
            'title' => $item['title'] ?? '',
            'description' => $item['description'] ?? '',
            'category' => VitrineBlock::normalizeLaboratoryCategory($item['category'] ?? null),
            'source_type' => ($item['source_type'] ?? null) === 'upload' ? 'upload' : 'url',
            'image_url' => filled($item['image_url'] ?? null) ? VitrineBlock::resolveImageAbsoluteUrl($item['image_url']) : '',
            'video_url' => filled($item['video_url'] ?? null) ? VitrineBlock::resolveImageAbsoluteUrl($item['video_url']) : '',
            'poster_source_type' => ($item['poster_source_type'] ?? null) === 'upload' ? 'upload' : 'url',
            'poster_url' => filled($item['poster_url'] ?? null) ? VitrineBlock::resolveImageAbsoluteUrl($item['poster_url']) : '',
            'preview_url' => null,
            'poster_preview_url' => null,
        ];
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { content: true, media: true },
        showMediaTypePicker: false,
        media: @js($mediaItems),
        categories: @js($categories),
        nextUid() {
            return 'media-' + Date.now() + '-' + Math.random().toString(36).slice(2, 9);
        },
        addMedia(type) {
            this.media.push({
                _uid: this.nextUid(),
                open: true,
                type: type === 'video' ? 'video' : 'image',
                title: '',
                description: '',
                category: 'equipe',
                source_type: 'url',
                image_url: '',
                video_url: '',
                poster_source_type: 'url',
                poster_url: '',
                preview_url: null,
                poster_preview_url: null,
            });
            this.showMediaTypePicker = false;
        },
        toggleMediaOpen(index) {
            if (!this.media[index]) return;
            this.media[index].open = !this.media[index].open;
        },
        moveMedia(index, direction) {
            const target = index + direction;
            if (target < 0 || target >= this.media.length) return;
            const [item] = this.media.splice(index, 1);
            this.media.splice(target, 0, item);
        },
        removeMedia(index) {
            this.media.splice(index, 1);
        },
        mediaLabel(item, index) {
            const title = (item.title || '').trim();
            if (title !== '') return title;
            return (item.type === 'video' ? 'Vidéo ' : 'Image ') + (index + 1);
        },
        photoPreview(item) { return item.preview_url || item.image_url || ''; },
        youtubeVideoId(url) {
            const match = String(url || '').match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([\w-]{11})/i);
            return match ? match[1] : null;
        },
        youtubeThumbnailUrl(url) {
            const id = this.youtubeVideoId(url);
            return id ? `https://img.youtube.com/vi/${id}/hqdefault.jpg` : '';
        },
        posterPreview(item) {
            return item.poster_preview_url || item.poster_url || this.youtubeThumbnailUrl(item.video_url) || '';
        },
        usesYoutubeThumbnail(item) {
            return !item.poster_preview_url && !item.poster_url && !!this.youtubeThumbnailUrl(item.video_url);
        },
        categoryLabel(key) { return this.categories[key]?.label || key; },
        onPhotoFileChange(event, item) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (item.preview_url?.startsWith('blob:')) URL.revokeObjectURL(item.preview_url);
            item.preview_url = URL.createObjectURL(file);
        },
        onPosterFileChange(event, item) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (item.poster_preview_url?.startsWith('blob:')) URL.revokeObjectURL(item.poster_preview_url);
            item.poster_preview_url = URL.createObjectURL(file);
        },
     }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'content',
            'title' => 'Contenu principal',
            'subtitle' => 'Titre et description de la page Galerie',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-emerald-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.content" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Badge (hero)</label>
                        <input type="text" name="content[section_label]" value="{{ $c['section_label'] ?? 'Galerie' }}" class="input-field w-full text-sm" placeholder="Galerie">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                        <input type="text" name="content[title]" value="{{ $c['title'] ?? '' }}" class="input-field w-full text-sm" placeholder="Notre équipe & nos installations">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description</label>
                    <textarea name="content[description]" rows="4" class="input-field w-full text-sm resize-y min-h-[100px]" placeholder="Présentation de l'équipe et du laboratoire…">{{ $c['description'] ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'media',
            'title' => 'Médias',
            'subtitle' => 'Développez, réordonnez et configurez chaque image ou vidéo',
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

        <div x-show="open.media" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="media.length === 0">
                    <div class="text-center py-8 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucun média — ajoutez une image ou une vidéo</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="media.length > 0">
                    <template x-for="(item, index) in media" :key="item._uid">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between gap-2 px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <button type="button"
                                        @click="toggleMediaOpen(index)"
                                        class="flex items-center gap-3 min-w-0 flex-1 text-left group">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary text-xs font-bold shrink-0" x-text="index + 1"></span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.68rem] font-semibold uppercase tracking-wide"
                                                  :class="item.type === 'video' ? 'bg-rose-500/10 text-rose-700' : 'bg-sky-500/10 text-sky-700'"
                                                  x-text="item.type === 'video' ? 'Vidéo' : 'Image'"></span>
                                            <p class="text-sm font-semibold text-primary truncate" x-text="mediaLabel(item, index)"></p>
                                        </div>
                                        <p class="text-xs text-secondary mt-0.5 truncate" x-text="categoryLabel(item.category)"></p>
                                    </div>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-border/70 bg-card text-secondary shrink-0 transition-all duration-200 group-hover:border-primary/30 group-hover:text-primary"
                                          :class="item.open ? 'rotate-180 bg-primary/5 border-primary/20 text-primary' : ''">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </span>
                                </button>

                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button"
                                            @click="moveMedia(index, -1)"
                                            :disabled="index === 0"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-secondary hover:text-primary hover:bg-primary/10 border border-transparent hover:border-primary/20 transition disabled:opacity-30 disabled:pointer-events-none"
                                            title="Monter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    </button>
                                    <button type="button"
                                            @click="moveMedia(index, 1)"
                                            :disabled="index === media.length - 1"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-secondary hover:text-primary hover:bg-primary/10 border border-transparent hover:border-primary/20 transition disabled:opacity-30 disabled:pointer-events-none"
                                            title="Descendre">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <button type="button"
                                            @click="removeMedia(index)"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition"
                                            title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <div x-show="item.open" x-cloak x-transition.opacity.duration.200ms class="p-4 sm:p-5 space-y-4">
                                <input type="hidden" :name="'content[media][' + index + '][type]'" :value="item.type">
                                <input type="hidden" :name="'content[media][' + index + '][source_type]'" x-model="item.source_type">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                        <input type="text" :name="'content[media][' + index + '][title]'" x-model="item.title" class="input-field w-full text-sm" :placeholder="item.type === 'video' ? 'Ex. Visite du laboratoire' : 'Ex. Équipe prothèse'">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Catégorie</label>
                                        <select :name="'content[media][' + index + '][category]'" x-model="item.category" class="input-field w-full text-sm">
                                            @foreach($categories as $key => $meta)
                                                <option value="{{ $key }}">{{ $meta['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description courte</label>
                                    <input type="text" :name="'content[media][' + index + '][description]'" x-model="item.description" class="input-field w-full text-sm" placeholder="Courte description…">
                                </div>

                                {{-- Image fields --}}
                                <template x-if="item.type === 'image'">
                                    <div class="space-y-3">
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide">Image</label>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" @click="item.source_type = 'url'" :class="item.source_type === 'url' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">URL</button>
                                            <button type="button" @click="item.source_type = 'upload'" :class="item.source_type === 'upload' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">Upload</button>
                                        </div>
                                        <template x-if="item.source_type === 'url'">
                                            <input type="text" :name="'content[media][' + index + '][image_url]'" x-model="item.image_url" placeholder="https://…" class="input-field w-full text-sm font-mono">
                                        </template>
                                        <template x-if="item.source_type === 'upload'">
                                            <div>
                                                <input type="hidden" :name="'content[media][' + index + '][image_url]'" :value="item.image_url">
                                                <label class="flex flex-col items-center justify-center w-full min-h-[88px] px-4 py-4 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer">
                                                    <input type="file" :name="'laboratory_media_uploads[' + index + ']'" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" class="sr-only" @change="onPhotoFileChange($event, item)">
                                                    <span class="text-sm font-medium text-primary">Choisir une image</span>
                                                    <span class="text-xs text-secondary mt-1">JPEG, PNG, WebP, GIF — max 10 Mo</span>
                                                </label>
                                            </div>
                                        </template>
                                        <div x-show="photoPreview(item)" class="max-w-xs">
                                            <img :src="photoPreview(item)" alt="" class="w-full rounded-xl border border-border object-cover aspect-[4/3]">
                                        </div>
                                    </div>
                                </template>

                                {{-- Video fields --}}
                                <template x-if="item.type === 'video'">
                                    <div class="space-y-4">
                                        <input type="hidden" :name="'content[media][' + index + '][poster_source_type]'" x-model="item.poster_source_type">
                                        <div class="space-y-3">
                                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide">Vidéo</label>
                                            <div class="flex flex-wrap gap-2">
                                                <button type="button" @click="item.source_type = 'url'" :class="item.source_type === 'url' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">URL</button>
                                                <button type="button" @click="item.source_type = 'upload'" :class="item.source_type === 'upload' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">Upload</button>
                                            </div>
                                            <template x-if="item.source_type === 'url'">
                                                <input type="url" :name="'content[media][' + index + '][video_url]'" x-model="item.video_url" placeholder="https://www.youtube.com/watch?v=…" class="input-field w-full text-sm font-mono">
                                            </template>
                                            <template x-if="item.source_type === 'upload'">
                                                <div>
                                                    <input type="hidden" :name="'content[media][' + index + '][video_url]'" :value="item.video_url">
                                                    <label class="flex flex-col items-center justify-center w-full min-h-[88px] px-4 py-4 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer">
                                                        <input type="file" :name="'laboratory_video_uploads[' + index + ']'" accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov" class="sr-only">
                                                        <span class="text-sm font-medium text-primary">Choisir une vidéo</span>
                                                        <span class="text-xs text-secondary mt-1">MP4, WebM, MOV — max 100 Mo</span>
                                                    </label>
                                                    <p class="mt-2 text-xs text-secondary break-all" x-show="item.video_url" x-text="'Fichier actuel : ' + item.video_url"></p>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="space-y-3">
                                            <label class="block text-xs font-semibold text-secondary uppercase tracking-wide">Miniature (optionnel)</label>
                                            <div class="flex flex-wrap gap-2">
                                                <button type="button" @click="item.poster_source_type = 'url'" :class="item.poster_source_type === 'url' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">URL</button>
                                                <button type="button" @click="item.poster_source_type = 'upload'" :class="item.poster_source_type === 'upload' ? 'bg-primary text-white' : 'bg-card border border-border text-secondary'" class="px-3 py-1.5 rounded-lg text-xs font-semibold">Upload</button>
                                            </div>
                                            <template x-if="item.poster_source_type === 'url'">
                                                <input type="text" :name="'content[media][' + index + '][poster_url]'" x-model="item.poster_url" placeholder="https://…" class="input-field w-full text-sm font-mono">
                                            </template>
                                            <template x-if="item.poster_source_type === 'upload'">
                                                <div>
                                                    <input type="hidden" :name="'content[media][' + index + '][poster_url]'" :value="item.poster_url">
                                                    <label class="flex flex-col items-center justify-center w-full min-h-[72px] px-4 py-3 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer">
                                                        <input type="file" :name="'laboratory_poster_uploads[' + index + ']'" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" class="sr-only" @change="onPosterFileChange($event, item)">
                                                        <span class="text-sm font-medium text-primary">Choisir une miniature</span>
                                                    </label>
                                                </div>
                                            </template>
                                            <div x-show="posterPreview(item)" class="max-w-[200px]">
                                                <img :src="posterPreview(item)" alt="" class="w-full rounded-lg border border-border object-cover aspect-video">
                                                <p x-show="usesYoutubeThumbnail(item)" class="mt-2 text-xs text-secondary">Miniature YouTube utilisée automatiquement</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60 space-y-3">
                    <div class="relative inline-flex flex-col items-start gap-2">
                        <button type="button"
                                @click="showMediaTypePicker = !showMediaTypePicker"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary to-primary/90 text-white text-sm font-semibold shadow-md shadow-primary/20 hover:opacity-95 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Ajouter média
                        </button>

                        <div x-show="showMediaTypePicker"
                             x-cloak
                             @click.outside="showMediaTypePicker = false"
                             class="flex flex-wrap gap-2 p-2 rounded-xl border border-border bg-card shadow-lg">
                            <button type="button"
                                    @click="addMedia('image')"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-semibold text-sky-700 bg-sky-500/10 hover:bg-sky-500/20 transition">
                                <i class="fas fa-image" aria-hidden="true"></i>
                                Image
                            </button>
                            <button type="button"
                                    @click="addMedia('video')"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-semibold text-rose-700 bg-rose-500/10 hover:bg-rose-500/20 transition">
                                <i class="fas fa-video" aria-hidden="true"></i>
                                Vidéo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
