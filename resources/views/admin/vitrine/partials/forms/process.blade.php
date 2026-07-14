@php
    $c = $content;
    $processSteps = collect($c['steps'] ?? [])->map(function ($step) {
        return [
            'title' => $step['title'] ?? '',
            'description' => $step['description'] ?? '',
            'detail_html' => $step['detail_html'] ?? '',
            'icon' => $step['icon'] ?? '',
            'is_active' => filter_var($step['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ];
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, steps: true },
        steps: @js($processSteps),
        htmlEditors: {},
        addStep() {
            this.steps.push({ title: '', description: '', detail_html: '', icon: '', is_active: true });
            const newIndex = this.steps.length - 1;
            this.$nextTick(() => this.initProcessStepHtmlEditor(newIndex));
        },
        removeStep(index) {
            this.destroyAllProcessStepHtmlEditors();
            this.steps.splice(index, 1);
            this.$nextTick(() => this.initAllProcessStepHtmlEditors());
        },
        initAllProcessStepHtmlEditors() {
            this.steps.forEach((_, index) => this.initProcessStepHtmlEditor(index));
        },
        initProcessStepHtmlEditor(index) {
            if (!this.open.steps || !this.steps[index]) return;
            const editorId = 'process-detail-html-' + index;
            if (this.htmlEditors[editorId]) return;

            const start = async () => {
                const tinymce = await (window.__vitrineServiceHtmlEditorReady || Promise.resolve(window.tinymce));
                if (!tinymce) return;

                const existing = tinymce.get(editorId);
                if (existing) {
                    this.htmlEditors[editorId] = existing;
                    return;
                }

                const textarea = document.getElementById(editorId);
                if (!textarea || textarea.dataset.tinymceInit === '1') return;

                textarea.value = this.steps[index].detail_html || '';
                textarea.dataset.tinymceInit = '1';

                tinymce.init({
                    ...window.__vitrineServiceHtmlEditorConfig,
                    selector: '#' + editorId,
                    setup: (editor) => {
                        editor.on('change input undo redo SetContent', () => {
                            if (this.steps[index]) {
                                this.steps[index].detail_html = editor.getContent();
                            }
                        });
                    },
                    init_instance_callback: (editor) => {
                        this.htmlEditors[editorId] = editor;
                    },
                });
            };

            this.$nextTick(() => start());
        },
        destroyAllProcessStepHtmlEditors() {
            if (!window.tinymce) return;
            Object.keys(this.htmlEditors).forEach((editorId) => {
                const editor = window.tinymce.get(editorId);
                if (editor) {
                    editor.remove();
                }
            });
            document.querySelectorAll('.process-detail-html-textarea').forEach((textarea) => {
                delete textarea.dataset.tinymceInit;
            });
            this.htmlEditors = {};
        }
     }"
     @vitrine-process-tab-open.window="$nextTick(() => { if (open.steps) initAllProcessStepHtmlEditors(); })">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'header',
            'title' => 'En-tête',
            'subtitle' => 'Titre et introduction de la section Processus',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-blue-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.header" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                @include('admin.vitrine.partials.section-en-tete', ['c' => $c])
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-gradient-to-b from-neutral-50/50 to-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'steps',
            'title' => 'Étapes du processus',
            'subtitle' => 'Activez et configurez chaque étape du workflow',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.steps" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="steps.length === 0">
                    <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune étape configurée</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="steps.length > 0">
                    <template x-for="(step, index) in steps" :key="'step-' + index">
                        <div class="rounded-xl border bg-card shadow-sm overflow-hidden transition-colors"
                             :class="step.is_active ? 'border-border' : 'border-amber-200/80 bg-amber-50/20'">
                            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 text-xs font-bold shrink-0" x-text="index + 1"></span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-primary truncate" x-text="(step.title || '').trim() !== '' ? step.title : ('Étape ' + (index + 1))"></p>
                                        <p class="text-xs text-secondary mt-0.5" x-text="step.is_active ? 'Visible sur le site' : 'Masquée sur le site'"></p>
                                    </div>
                                </div>
                                <button type="button" @click="removeStep(index)"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 border border-transparent hover:border-danger/20 transition-colors shrink-0"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4 sm:p-5 space-y-4">
                                <label class="inline-flex items-center gap-3 cursor-pointer group pb-4 mb-4 border-b border-border/50 w-full">
                                    <input type="hidden" :name="'content[steps][' + index + '][is_active]'" value="0">
                                    <input type="checkbox"
                                           :name="'content[steps][' + index + '][is_active]'" value="1"
                                           x-model="step.is_active"
                                           class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                                    <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher cette étape sur le site</span>
                                </label>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                    <input type="text" :name="'content[steps][' + index + '][title]'" x-model="step.title" placeholder="Consultation" class="input-field w-full text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Description</label>
                                    <textarea :name="'content[steps][' + index + '][description]'" x-model="step.description" rows="2" placeholder="Décrivez cette étape…" class="input-field w-full text-sm resize-y"></textarea>
                                </div>

                                @include('admin.vitrine.partials.process-step-detail-html-editor')
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une étape', 'click' => 'addStep()'])
                </div>
            </div>
        </div>
    </section>
</div>
