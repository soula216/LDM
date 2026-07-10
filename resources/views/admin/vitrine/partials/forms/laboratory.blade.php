@php
    use App\Models\VitrineBlock;

    $c = $content;
    $categories = VitrineBlock::laboratoryCategories();
    $photos = collect($c['photos'] ?? [])->map(function ($photo) use ($categories) {
        $category = VitrineBlock::normalizeLaboratoryCategory($photo['category'] ?? null);

        return [
            'image_url' => filled($photo['image_url'] ?? null) ? VitrineBlock::resolveImageAbsoluteUrl($photo['image_url']) : '',
            'source_type' => ($photo['source_type'] ?? null) === 'upload' ? 'upload' : 'url',
            'title' => $photo['title'] ?? '',
            'description' => $photo['description'] ?? '',
            'category' => $category,
            'preview_url' => null,
        ];
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { content: true, photos: true },
        photos: @js($photos),
        categories: @js($categories),
        addPhoto() {
            this.photos.push({
                image_url: '',
                source_type: 'url',
                title: '',
                description: '',
                category: 'equipe',
                preview_url: null,
            });
        },
        photoPreview(photo) { return photo.preview_url || photo.image_url || ''; },
        categoryLabel(key) { return this.categories[key]?.label || key; },
        onPhotoFileChange(event, photo) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (photo.preview_url?.startsWith('blob:')) URL.revokeObjectURL(photo.preview_url);
            photo.preview_url = URL.createObjectURL(file);
        },
     }">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'content',
            'title' => 'Contenu principal',
            'subtitle' => 'Titre et description de la page Laboratoire / Équipe',
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
                        <input type="text" name="content[section_label]" value="{{ $c['section_label'] ?? 'Laboratoire / Équipe' }}" class="input-field w-full text-sm" placeholder="Laboratoire / Équipe">
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
            'section' => 'photos',
            'title' => 'Photos',
            'subtitle' => 'Équipe, laboratoire et machines — nom, description et catégorie pour chaque photo',
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
                        <p class="text-sm text-secondary font-medium">Aucune photo — ajoutez des visuels de l'équipe, du laboratoire ou des équipements</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="photos.length > 0">
                    <template x-for="(photo, index) in photos" :key="'lab-photo-' + index">
                        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-xs font-bold text-primary uppercase tracking-wide truncate" x-text="photo.title || ('Photo ' + (index + 1))"></span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 text-[0.68rem] font-semibold uppercase tracking-wide" x-text="categoryLabel(photo.category)"></span>
                                </div>
                                <button type="button" @click="photos.splice(index, 1)" class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            <div class="p-4 sm:p-5 space-y-4">
                                <input type="hidden" :name="'content[photos][' + index + '][source_type]'" x-model="photo.source_type">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Nom de la photo</label>
                                        <input type="text" :name="'content[photos][' + index + '][title]'" x-model="photo.title" class="input-field w-full text-sm" placeholder="Ex. Équipe prothèse">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Catégorie</label>
                                        <select :name="'content[photos][' + index + '][category]'" x-model="photo.category" class="input-field w-full text-sm">
                                            @foreach($categories as $key => $meta)
                                                <option value="{{ $key }}">{{ $meta['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description courte</label>
                                    <input type="text" :name="'content[photos][' + index + '][description]'" x-model="photo.description" class="input-field w-full text-sm" placeholder="Courte description…">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Image</label>
                                    <div class="flex flex-wrap gap-2 mb-3">
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
                                                <input type="file" :name="'laboratory_photo_uploads[' + index + ']'" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" class="sr-only" @change="onPhotoFileChange($event, photo)">
                                                <span class="text-sm font-medium text-primary">Choisir une image</span>
                                                <span class="text-xs text-secondary mt-1">JPEG, PNG, WebP, GIF — max 10 Mo</span>
                                            </label>
                                        </div>
                                    </template>
                                </div>
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
</div>
