<input type="hidden" :name="'content[documents][' + index + '][file_type]'" x-model="doc.file_type">
<input type="hidden" :name="'content[documents][' + index + '][file_source_type]'" x-model="doc.file_source_type">

<template x-if="doc.file_type === 'video'">
    <div class="flex flex-wrap gap-2 mb-4">
        <button type="button"
                @click="doc.file_source_type = 'upload'"
                :class="doc.file_source_type === 'upload' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
            Upload
        </button>
        <button type="button"
                @click="doc.file_source_type = 'url'"
                :class="doc.file_source_type === 'url' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
            URL
        </button>
    </div>
</template>

<template x-if="doc.file_type !== 'video' || doc.file_source_type === 'upload'">
    <div>
        <input type="hidden" :name="'content[documents][' + index + '][file_url]'" :value="doc.file_url">
        <input type="hidden" :name="'content[documents][' + index + '][file_name]'" :value="doc.file_name">
        <label class="flex flex-col items-center justify-center w-full min-h-[96px] px-4 py-5 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer transition-all">
            <input type="file"
                   :name="'academy_file_uploads[' + index + ']'"
                   :accept="fileTypeAccept(doc.file_type)"
                   class="sr-only"
                   @change="onFileChange($event, doc)">
            <svg class="w-8 h-8 text-secondary/50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <span class="text-sm font-medium text-primary" x-text="'Cliquez pour choisir un fichier ' + fileTypeLabel(doc.file_type)"></span>
            <span class="text-xs text-secondary mt-1" x-text="fileTypeUploadHint(doc.file_type)"></span>
        </label>
        <p class="mt-2 text-xs text-secondary break-all" x-show="doc.file_url && !doc.has_new_file">
            Fichier actuel :
            <a :href="doc.file_url" target="_blank" rel="noopener" class="text-primary underline" x-text="doc.file_name || 'Voir le fichier'"></a>
        </p>
        <p class="mt-2 text-xs text-emerald-600 font-medium" x-show="doc.has_new_file">
            Nouveau fichier sélectionné (<span x-text="doc.file_name"></span>) — enregistrez pour publier
        </p>
    </div>
</template>

<template x-if="doc.file_type === 'video' && doc.file_source_type === 'url'">
    <div>
        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">URL de la vidéo</label>
        <input type="url"
               :name="'content[documents][' + index + '][file_url]'"
               x-model="doc.file_url"
               placeholder="https://www.youtube.com/watch?v=… ou https://exemple.com/video.mp4"
               class="input-field w-full text-sm font-mono">
        <input type="hidden" :name="'content[documents][' + index + '][file_name]'" :value="doc.file_name">
        <p class="mt-2 text-xs text-secondary">Lien YouTube, Vimeo ou URL directe vers un fichier vidéo.</p>
    </div>
</template>
