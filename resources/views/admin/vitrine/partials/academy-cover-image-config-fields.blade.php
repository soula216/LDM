<input type="hidden" :name="'content[documents][' + index + '][cover_image_source_type]'" x-model="doc.cover_image_source_type">

<div class="flex flex-wrap gap-2 mb-4">
    <button type="button"
            @click="doc.cover_image_source_type = 'url'"
            :class="doc.cover_image_source_type === 'url' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
        URL
    </button>
    <button type="button"
            @click="doc.cover_image_source_type = 'upload'"
            :class="doc.cover_image_source_type === 'upload' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
        Upload
    </button>
</div>

<template x-if="doc.cover_image_source_type === 'url'">
    <div>
        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">URL de l'image</label>
        <input type="text"
               :name="'content[documents][' + index + '][cover_image_url]'"
               x-model="doc.cover_image_url"
               placeholder="https://exemple.com/couverture.jpg"
               class="input-field w-full text-sm font-mono">
        <p class="mt-2 text-xs text-secondary">Image affichée en arrière-plan de la carte sur le site.</p>
    </div>
</template>

<template x-if="doc.cover_image_source_type === 'upload'">
    <div>
        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Fichier image</label>
        <input type="hidden" :name="'content[documents][' + index + '][cover_image_url]'" :value="doc.cover_image_url">
        <label class="flex flex-col items-center justify-center w-full min-h-[88px] px-4 py-4 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer transition-all">
            <input type="file"
                   :name="'academy_cover_uploads[' + index + ']'"
                   accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
                   class="sr-only"
                   @change="onCoverImageFileChange($event, doc)">
            <span class="text-sm font-medium text-primary">Cliquez pour choisir une image</span>
            <span class="text-xs text-secondary mt-1">JPEG, PNG, WebP, GIF — max 5 Mo</span>
        </label>
        <p class="mt-2 text-xs text-secondary break-all" x-show="doc.cover_image_url && !doc.cover_preview_url">
            Fichier actuel : <span x-text="doc.cover_image_url"></span>
        </p>
        <p class="mt-2 text-xs text-emerald-600 font-medium" x-show="doc.cover_preview_url">
            Nouvelle image sélectionnée — enregistrez pour appliquer
        </p>
    </div>
</template>

<div class="mt-4">
    <p class="text-xs font-semibold text-secondary uppercase tracking-wide mb-2">Aperçu</p>
    <div class="inline-block w-full max-w-xs aspect-[16/9] rounded-xl border border-border bg-neutral-50/80 overflow-hidden relative">
        <template x-if="docCoverImagePreview(doc)">
            <img :src="docCoverImagePreview(doc)" alt="" class="w-full h-full object-cover" @@error="$el.style.display='none'">
        </template>
        <template x-if="!docCoverImagePreview(doc)">
            <div class="w-full h-full flex items-center justify-center text-[10px] text-secondary text-center px-2">Aucune image de fond</div>
        </template>
    </div>
</div>
