<div
    class="service-html-editor-field"
    x-init="
        $watch('open.steps', (isOpen) => {
            if (isOpen) {
                $nextTick(() => initProcessStepHtmlEditor(index));
            }
        });
        if (open.steps) {
            $nextTick(() => initProcessStepHtmlEditor(index));
        }
    "
    @vitrine-process-tab-open.window="$nextTick(() => { if (open.steps) initProcessStepHtmlEditor(index); })"
    @vitrine-about-tab-open.window="$nextTick(() => { if (open.steps) initProcessStepHtmlEditor(index); })"
>
    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Explication détaillée</label>
    <input type="hidden" :name="'content[steps][' + index + '][detail_html]'" :value="step.detail_html || ''">
    <textarea
        :id="'process-detail-html-' + index"
        class="process-detail-html-textarea"
    ></textarea>
    <p class="mt-1.5 text-xs text-secondary">Éditeur visuel : titres, listes, liens, images, tableaux, etc.</p>
</div>
