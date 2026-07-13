<div class="rounded-xl border border-border/70 bg-neutral-50/40 p-4 sm:p-5 space-y-4">
    <div>
        <h4 class="text-sm font-semibold text-primary">Sections de la page détail</h4>
        <p class="text-xs text-secondary mt-1">Ajoutez une ou plusieurs sections avec un titre, une explication et des photos.</p>
    </div>

    <template x-if="!item.sections || item.sections.length === 0">
        <div class="text-center py-6 px-4 rounded-xl border-2 border-dashed border-border bg-card/80">
            <p class="text-sm text-secondary font-medium">Aucune section — le contenu détaillé sera vide</p>
        </div>
    </template>

    <div class="space-y-4" x-show="item.sections && item.sections.length > 0">
        <template x-for="(section, sectionIndex) in item.sections" :key="'service-section-' + index + '-' + sectionIndex">
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                    <button type="button"
                            @click="section.open = !section.open"
                            class="flex items-center gap-3 min-w-0 flex-1 text-left group">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 text-xs font-bold shrink-0"
                              x-text="sectionIndex + 1"></span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-primary truncate"
                               x-text="(section.title || '').trim() !== '' ? section.title : ('Section ' + (sectionIndex + 1))"></p>
                            <p class="text-xs text-secondary mt-0.5"
                               x-text="sectionPhotosCount(section) + ' photo(s)'"></p>
                        </div>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-border/70 bg-card text-secondary shrink-0 transition-all duration-200 group-hover:border-primary/30 group-hover:text-primary"
                              :class="section.open ? 'rotate-180 bg-primary/5 border-primary/20 text-primary' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </button>
                    <button type="button"
                            @click="removeServiceSection(item, sectionIndex)"
                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors shrink-0"
                            title="Supprimer la section">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>

                <div x-show="section.open" x-cloak x-transition.opacity.duration.200ms class="p-4 sm:p-5 space-y-4 border-t border-border/40">
                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre de la section</label>
                        <input type="text"
                               :name="'content[items][' + index + '][sections][' + sectionIndex + '][title]'"
                               x-model="section.title"
                               placeholder="Ex. Notre processus CFAO"
                               class="input-field w-full text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Explication</label>
                        <textarea :name="'content[items][' + index + '][sections][' + sectionIndex + '][description]'"
                                  x-model="section.description"
                                  rows="4"
                                  placeholder="Décrivez cette partie du service…"
                                  class="input-field w-full text-sm resize-y min-h-[100px]"></textarea>
                        <p class="mt-1.5 text-xs text-secondary">Séparez les paragraphes par une ligne vide.</p>
                    </div>

                    <div class="pt-2 border-t border-border/50 space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-secondary uppercase tracking-wide mb-2">Photos</p>
                            <div class="service-section-dropzone relative flex flex-col items-center justify-center w-full min-h-[112px] px-4 py-5 border-2 border-dashed rounded-xl cursor-pointer transition-all"
                                 :class="section.photoDropActive
                                    ? 'border-primary bg-primary/10 ring-2 ring-primary/20 scale-[1.01]'
                                    : 'border-border bg-card hover:border-primary/40 hover:bg-primary/5'"
                                 @click="$el.querySelector('input[type=file]')?.click()"
                                 @dragenter.prevent="onSectionPhotosDragOver($event, section)"
                                 @dragover.prevent="onSectionPhotosDragOver($event, section)"
                                 @dragleave.prevent="onSectionPhotosDragLeave($event, section)"
                                 @drop.prevent="onSectionPhotosDrop($event, section, 'service-section-photos-' + index + '-' + sectionIndex)">
                                <input type="file"
                                       :name="'service_section_photo_uploads[' + index + '][' + sectionIndex + '][]'"
                                       :id="'service-section-photos-' + index + '-' + sectionIndex"
                                       accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                                       class="sr-only"
                                       multiple
                                       @click.stop
                                       @change="onSectionPhotosMultipleChange($event, section)">
                                <svg class="w-8 h-8 mb-2 transition-colors"
                                     :class="section.photoDropActive ? 'text-primary' : 'text-primary/70'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-primary"
                                      x-text="section.photoDropActive ? 'Déposez les images ici' : 'Glissez-déposez ou cliquez pour choisir'"></span>
                                <span class="text-xs text-secondary mt-1 text-center">Plusieurs images — JPEG, PNG, WebP, GIF — max 10 Mo par image</span>
                            </div>
                            <p class="mt-2 text-xs text-secondary"
                               x-show="section.pendingPreviews?.length > 0"
                               x-text="section.pendingPreviews.length + ' nouvelle(s) photo(s) seront ajoutée(s) à l\'enregistrement'"></p>
                        </div>

                        <template x-if="sectionPhotosCount(section) === 0">
                            <p class="text-xs text-secondary italic">Aucune photo dans cette section.</p>
                        </template>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3"
                             x-show="sectionPhotosCount(section) > 0">
                            <template x-for="(photo, photoIndex) in section.photos" :key="'service-photo-saved-' + index + '-' + sectionIndex + '-' + photoIndex">
                                <div class="rounded-lg border border-border/70 bg-card overflow-hidden flex flex-col">
                                    <div class="flex items-center justify-between gap-2 px-3 py-2 bg-neutral-50/80 border-b border-border/60">
                                        <span class="text-[10px] font-bold text-primary uppercase tracking-wide truncate min-w-0"
                                              x-text="(photo.title || '').trim() !== '' ? photo.title : ('Photo ' + (photoIndex + 1))"></span>
                                        <button type="button"
                                                @click.stop="removeSavedSectionPhoto(section, photoIndex)"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors shrink-0"
                                                title="Supprimer la photo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="aspect-[4/3] bg-neutral-100 overflow-hidden">
                                        <img :src="sectionPhotoPreview(photo)"
                                             alt=""
                                             class="w-full h-full object-cover"
                                             @@error="$el.style.display='none'">
                                    </div>
                                    <div class="p-2.5 space-y-1.5">
                                        <input type="hidden"
                                               :name="'content[items][' + index + '][sections][' + sectionIndex + '][photos][' + photoIndex + '][source_type]'"
                                               value="upload">
                                        <input type="hidden"
                                               :name="'content[items][' + index + '][sections][' + sectionIndex + '][photos][' + photoIndex + '][image_url]'"
                                               :value="photo.image_url">
                                        <label class="block text-[10px] font-semibold text-secondary uppercase tracking-wide">Légende</label>
                                        <input type="text"
                                               :name="'content[items][' + index + '][sections][' + sectionIndex + '][photos][' + photoIndex + '][title]'"
                                               x-model="photo.title"
                                               placeholder="Optionnel"
                                               class="input-field w-full text-xs">
                                    </div>
                                </div>
                            </template>

                            <template x-for="(pending, pendingIndex) in section.pendingPreviews || []" :key="'service-photo-pending-' + index + '-' + sectionIndex + '-' + pendingIndex">
                                <div class="rounded-lg border border-dashed border-primary/40 bg-primary/5 overflow-hidden flex flex-col">
                                    <div class="flex items-center justify-between gap-2 px-3 py-2 bg-primary/5 border-b border-primary/15">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-primary text-white text-[10px] font-semibold uppercase tracking-wide shrink-0">Nouveau</span>
                                            <span class="text-[11px] text-secondary truncate" x-text="pending.name"></span>
                                        </div>
                                        <button type="button"
                                                @click.stop="removePendingSectionPhoto(section, pendingIndex, 'service-section-photos-' + index + '-' + sectionIndex)"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors shrink-0"
                                                title="Retirer de la sélection">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="aspect-[4/3] bg-neutral-100 overflow-hidden">
                                        <img :src="pending.preview_url"
                                             alt=""
                                             class="w-full h-full object-cover opacity-90">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="pt-2">
        @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une section', 'click' => 'addServiceSection(item)'])
    </div>
</div>
