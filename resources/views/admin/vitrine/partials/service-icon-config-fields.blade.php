<input type="hidden" :name="'content[items][' + index + '][icon_source_type]'" x-model="item.icon_source_type">

<div class="flex flex-wrap gap-2 mb-4">
    <button type="button"
            @click="item.icon_source_type = 'url'"
            :class="item.icon_source_type === 'url' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
        URL
    </button>
    <button type="button"
            @click="item.icon_source_type = 'upload'"
            :class="item.icon_source_type === 'upload' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
        Upload
    </button>
</div>

<template x-if="item.icon_source_type === 'url'">
    <div>
        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">URL de l'icône</label>
        <input type="text"
               :name="'content[items][' + index + '][icon_url]'"
               x-model="item.icon_url"
               placeholder="https://exemple.com/icone-service.png"
               class="input-field w-full text-sm font-mono">
        <p class="mt-2 text-xs text-secondary">Adresse complète de l'image (PNG, SVG, WebP…).</p>
    </div>
</template>

<template x-if="item.icon_source_type === 'upload'">
    <div>
        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Fichier icône</label>
        <input type="hidden" :name="'content[items][' + index + '][icon_url]'" :value="item.icon_url">
        <label class="flex flex-col items-center justify-center w-full min-h-[88px] px-4 py-4 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer transition-all">
            <input type="file"
                   :name="'service_icon_uploads[' + index + ']'"
                   accept="image/jpeg,image/jpg,image/png,image/webp,image/gif,image/svg+xml"
                   class="sr-only"
                   @change="onItemIconFileChange($event, item)">
            <span class="text-sm font-medium text-primary">Cliquez pour choisir une icône</span>
            <span class="text-xs text-secondary mt-1">PNG, SVG, JPEG, WebP — max 5 Mo</span>
        </label>
        <p class="mt-2 text-xs text-secondary break-all" x-show="item.icon_url && !item.preview_url">
            Fichier actuel : <span x-text="item.icon_url"></span>
        </p>
        <p class="mt-2 text-xs text-emerald-600 font-medium" x-show="item.preview_url">
            Nouvelle icône sélectionnée — enregistrez pour appliquer
        </p>
    </div>
</template>

<div class="mt-4">
    <p class="text-xs font-semibold text-secondary uppercase tracking-wide mb-2">Aperçu de l'icône</p>
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl border border-border bg-neutral-50/80">
        <template x-if="itemIconPreview(item)">
            <img :src="itemIconPreview(item)" alt="" class="w-8 h-8 object-contain" @@error="$el.style.display='none'">
        </template>
        <template x-if="!itemIconPreview(item)">
            <span class="text-[10px] text-secondary text-center px-1">Aucune icône</span>
        </template>
    </div>
</div>
