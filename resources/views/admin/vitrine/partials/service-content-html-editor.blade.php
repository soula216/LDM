<div
    class="service-html-editor-field"
    x-init="
        $watch('item.open', (open) => {
            if (open) {
                $nextTick(() => initServiceHtmlEditor(index));
            }
        });
        if (item.open) {
            $nextTick(() => initServiceHtmlEditor(index));
        }
    "
    @vitrine-services-tab-open.window="$nextTick(() => { if (item.open) initServiceHtmlEditor(index); })"
>
    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Contenu de la page détail</label>
    <textarea
        :id="'service-content-html-' + index"
        :name="'content[items][' + index + '][content_html]'"
        class="service-content-html-textarea"
    ></textarea>
    <p class="mt-1.5 text-xs text-secondary">Éditeur visuel : titres, listes, liens, images, tableaux, etc.</p>
</div>
