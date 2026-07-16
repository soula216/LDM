@php
    $c = $content;
    $contentHtml = $c['content_html'] ?? '';
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { content: true },
        contentHtml: @js($contentHtml),
        htmlEditor: null,
        initMentionsLegalesHtmlEditor() {
            if (!this.open.content) return;
            const editorId = 'mentions-legales-content-html';
            if (this.htmlEditor) return;

            const start = async () => {
                const tinymce = await (window.__vitrineServiceHtmlEditorReady || Promise.resolve(window.tinymce));
                if (!tinymce) return;

                const existing = tinymce.get(editorId);
                if (existing) {
                    this.htmlEditor = existing;
                    return;
                }

                const textarea = document.getElementById(editorId);
                if (!textarea || textarea.dataset.tinymceInit === '1') return;

                textarea.value = this.contentHtml || '';
                textarea.dataset.tinymceInit = '1';

                tinymce.init({
                    ...window.__vitrineServiceHtmlEditorConfig,
                    selector: '#' + editorId,
                    setup: (editor) => {
                        editor.on('change input undo redo SetContent', () => {
                            this.contentHtml = editor.getContent();
                        });
                    },
                    init_instance_callback: (editor) => {
                        this.htmlEditor = editor;
                    },
                });
            };

            this.$nextTick(() => start());
        },
        syncMentionsLegalesHtmlEditor() {
            if (this.htmlEditor) {
                this.contentHtml = this.htmlEditor.getContent();
            }
        }
     }"
     x-init="
        $watch('open.content', (isOpen) => {
            if (isOpen) $nextTick(() => initMentionsLegalesHtmlEditor());
        });
        if (open.content) $nextTick(() => initMentionsLegalesHtmlEditor());
     "
     @vitrine-mentions-legales-tab-open.window="$nextTick(() => { if (open.content) initMentionsLegalesHtmlEditor(); })"
     @submit.document="if ($event.target.closest('form')?.contains($el)) syncMentionsLegalesHtmlEditor()">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'content',
            'title' => 'Contenu de la page',
            'subtitle' => 'Titre et texte affichés sur /mentions-legales',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-slate-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-slate-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.content" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6 space-y-5">
                @include('admin.vitrine.partials.field', [
                    'label' => 'Badge (optionnel)',
                    'name' => 'content[section_label]',
                    'value' => $c['section_label'] ?? 'Mentions légales',
                    'placeholder' => 'Mentions légales',
                ])

                @include('admin.vitrine.partials.field', [
                    'label' => 'Titre',
                    'name' => 'content[section_title]',
                    'value' => $c['section_title'] ?? 'Mentions légales',
                    'placeholder' => 'Mentions légales',
                ])

                <div class="service-html-editor-field">
                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description</label>
                    <input type="hidden" name="content[content_html]" :value="contentHtml || ''">
                    <textarea
                        id="mentions-legales-content-html"
                        class="mentions-legales-content-html-textarea"
                    ></textarea>
                    <p class="mt-1.5 text-xs text-secondary">Éditeur visuel : titres, listes, liens, images, tableaux, etc.</p>
                </div>
            </div>
        </div>
    </section>
</div>
