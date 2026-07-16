<div
    class="service-html-editor-field"
    x-init="
        $watch('item.open', (open) => {
            if (open) {
                $nextTick(() => initRecrutementHtmlEditor(index));
            }
        });
        if (item.open) {
            $nextTick(() => initRecrutementHtmlEditor(index));
        }
    "
    @vitrine-recrutement-tab-open.window="$nextTick(() => { if (item.open) initRecrutementHtmlEditor(index); })"
>
    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description de l’emploi</label>
    <textarea
        :id="'recrutement-description-html-' + index"
        :name="'content[items][' + index + '][description_html]'"
        class="recrutement-description-html-textarea"
    ></textarea>
    <p class="mt-1.5 text-xs text-secondary">Éditeur visuel : titres, listes, liens, images, tableaux, etc.</p>
</div>
