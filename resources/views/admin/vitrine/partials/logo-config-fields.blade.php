@props([
    'logoAlt' => '',
    'logoAltPlaceholder' => 'LDM - Digital Max',
])

<input type="hidden" name="content[logo_source_type]" x-model="logo.source_type">

<div class="flex flex-wrap gap-2">
    <button type="button"
            @click="logo.source_type = 'url'"
            :class="logo.source_type === 'url' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
        URL
    </button>
    <button type="button"
            @click="logo.source_type = 'upload'"
            :class="logo.source_type === 'upload' ? 'bg-primary text-white shadow-sm' : 'bg-card text-secondary border border-border hover:border-primary/30'"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
        Upload
    </button>
</div>

<template x-if="logo.source_type === 'url'">
    <div>
        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">URL du logo</label>
        <input type="text"
               name="content[logo_url]"
               x-model="logo.image_url"
               placeholder="logo_ldm.png ou https://exemple.com/logo.png"
               class="input-field w-full text-sm font-mono">
        <p class="mt-2 text-xs text-secondary">Chemin public (ex. logo_ldm.png) ou URL complète de l'image.</p>
    </div>
</template>

<template x-if="logo.source_type === 'upload'">
    <div>
        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Fichier logo</label>
        <input type="hidden" name="content[logo_url]" :value="logo.image_url">
        <label class="flex flex-col items-center justify-center w-full min-h-[100px] px-4 py-5 border-2 border-dashed border-border rounded-xl bg-neutral-50/50 hover:border-primary/40 cursor-pointer transition-all">
            <input type="file"
                   name="logo_upload"
                   accept="image/jpeg,image/jpg,image/png,image/webp,image/gif,image/svg+xml"
                   class="sr-only"
                   @change="onLogoFileChange($event)">
            <span class="text-sm font-medium text-primary">Cliquez pour choisir le logo</span>
            <span class="text-xs text-secondary mt-1">PNG, SVG, JPEG, WebP — max 5 Mo</span>
        </label>
        <p class="mt-2 text-xs text-secondary break-all" x-show="logo.image_url && !logo.preview_url">
            Fichier actuel : <span x-text="logo.image_url"></span>
        </p>
    </div>
</template>

<div>
    @include('admin.vitrine.partials.field', [
        'label' => 'Texte alternatif du logo',
        'name' => 'content[logo_alt]',
        'value' => $logoAlt,
        'placeholder' => $logoAltPlaceholder,
    ])
</div>

<div>
    <p class="text-xs font-semibold text-secondary uppercase tracking-wide mb-2">Aperçu</p>
    <div class="inline-flex items-center justify-center p-4 rounded-xl border border-border bg-neutral-50/80 min-h-[72px] min-w-[160px]">
        <template x-if="logoPreview()">
            <img :src="logoPreview()" alt="Aperçu du logo" class="max-h-12 w-auto object-contain" @@error="$el.style.display='none'">
        </template>
        <template x-if="!logoPreview()">
            <span class="text-xs text-secondary">Aucun logo configuré</span>
        </template>
    </div>
</div>
