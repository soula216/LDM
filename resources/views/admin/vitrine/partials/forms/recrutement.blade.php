@php
    use App\Models\VitrineBlock;

    $c = $content;
    $employmentTypeOptions = VitrineBlock::recruitmentEmploymentTypes();
    $genderOptions = VitrineBlock::recruitmentGenderOptions();

    $items = collect($c['items'] ?? [])->values()->map(function ($item, $index) use ($employmentTypeOptions, $genderOptions) {
        $types = collect($item['employment_types'] ?? [])
            ->map(fn ($type) => trim((string) $type))
            ->filter(fn ($type) => in_array($type, $employmentTypeOptions, true))
            ->values()
            ->all();

        $gender = trim((string) ($item['gender'] ?? 'Indifférent'));
        if (! in_array($gender, $genderOptions, true)) {
            $gender = 'Indifférent';
        }

        return [
            '_uid' => 'job-' . $index . '-' . uniqid(),
            'open' => false,
            'title' => $item['title'] ?? '',
            'vacancies' => max(0, (int) ($item['vacancies'] ?? 1)),
            'employment_types' => $types,
            'experience' => $item['experience'] ?? '',
            'education_level' => $item['education_level'] ?? '',
            'languages' => $item['languages'] ?? '',
            'gender' => $gender,
            'description_html' => $item['description_html'] ?? '',
            'is_active' => filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_expired' => filter_var($item['is_expired'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    })->values()->all();
@endphp

<div class="vitrine-tab-form space-y-6 sm:space-y-8"
     x-data="{
        open: { header: true, items: true },
        items: @js($items),
        employmentTypeOptions: @js($employmentTypeOptions),
        genderOptions: @js($genderOptions),
        htmlEditors: {},
        nextUid() {
            return 'job-' + Date.now() + '-' + Math.random().toString(36).slice(2, 9);
        },
        addItem() {
            this.items.push({
                _uid: this.nextUid(),
                open: true,
                title: '',
                vacancies: 1,
                employment_types: [],
                experience: '',
                education_level: '',
                languages: '',
                gender: 'Indifférent',
                description_html: '',
                is_active: true,
                is_expired: false,
            });
            const newIndex = this.items.length - 1;
            this.$nextTick(() => this.initRecrutementHtmlEditor(newIndex));
        },
        removeItem(index) {
            this.destroyAllRecrutementHtmlEditors();
            this.items.splice(index, 1);
            this.$nextTick(() => this.initAllRecrutementHtmlEditors());
        },
        toggleItemOpen(index) {
            if (!this.items[index]) return;
            this.items[index].open = !this.items[index].open;
            if (this.items[index].open) {
                this.$nextTick(() => this.initRecrutementHtmlEditor(index));
            }
        },
        moveItem(index, direction) {
            const target = index + direction;
            if (target < 0 || target >= this.items.length) return;
            this.destroyAllRecrutementHtmlEditors();
            const [item] = this.items.splice(index, 1);
            this.items.splice(target, 0, item);
            this.$nextTick(() => this.initAllRecrutementHtmlEditors());
        },
        toggleEmploymentType(item, type) {
            const list = item.employment_types || [];
            const pos = list.indexOf(type);
            if (pos === -1) list.push(type);
            else list.splice(pos, 1);
            item.employment_types = list;
        },
        hasEmploymentType(item, type) {
            return (item.employment_types || []).includes(type);
        },
        itemLabel(item, index) {
            const title = (item.title || '').trim();
            return title !== '' ? title : ('Offre ' + (index + 1));
        },
        initAllRecrutementHtmlEditors() {
            this.items.forEach((item, index) => {
                if (item.open) this.initRecrutementHtmlEditor(index);
            });
        },
        initRecrutementHtmlEditor(index) {
            if (!this.open.items || !this.items[index]?.open) return;
            const editorId = 'recrutement-description-html-' + index;
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

                textarea.value = this.items[index].description_html || '';
                textarea.dataset.tinymceInit = '1';

                tinymce.init({
                    ...window.__vitrineServiceHtmlEditorConfig,
                    selector: '#' + editorId,
                    setup: (editor) => {
                        editor.on('change input undo redo SetContent', () => {
                            if (this.items[index]) {
                                this.items[index].description_html = editor.getContent();
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
        destroyAllRecrutementHtmlEditors() {
            if (!window.tinymce) return;
            Object.keys(this.htmlEditors).forEach((editorId) => {
                const editor = window.tinymce.get(editorId);
                if (editor) editor.remove();
            });
            document.querySelectorAll('.recrutement-description-html-textarea').forEach((textarea) => {
                delete textarea.dataset.tinymceInit;
            });
            this.htmlEditors = {};
        },
     }"
     x-init="
        $watch('open.items', (isOpen) => {
            if (isOpen) $nextTick(() => initAllRecrutementHtmlEditors());
        });
     "
     @vitrine-recrutement-tab-open.window="$nextTick(() => initAllRecrutementHtmlEditors())">

    <section class="rounded-2xl border border-border bg-card overflow-hidden">
        @component('admin.vitrine.partials.collapsible-header', [
            'section' => 'header',
            'title' => 'En-tête',
            'subtitle' => 'Titre et introduction de la page Recrutement',
            'headerClass' => 'border-b border-border/60 bg-gradient-to-r from-emerald-500/5 to-transparent',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
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
            'section' => 'items',
            'title' => 'Offres d’emploi',
            'subtitle' => 'Développez, réordonnez et configurez chaque offre',
            'headerClass' => 'border-b border-border/60 bg-card/80',
        ])
            @slot('icon')
                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            @endslot
        @endcomponent

        <div x-show="open.items" x-cloak x-transition.opacity.duration.200ms>
            <div class="p-4 sm:p-6">
                <template x-if="items.length === 0">
                    <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-border bg-neutral-50/50 mb-4">
                        <p class="text-sm text-secondary font-medium">Aucune offre configurée</p>
                    </div>
                </template>

                <div class="space-y-4" x-show="items.length > 0">
                    <template x-for="(item, index) in items" :key="item._uid">
                        <div class="rounded-xl border bg-card shadow-sm overflow-hidden transition-colors"
                             :class="item.is_expired
                                ? 'border-rose-200/80 bg-rose-50/30'
                                : (item.is_active ? 'border-border' : 'border-amber-200/80 bg-amber-50/20')">
                            <div class="flex items-center justify-between gap-2 px-4 py-3 bg-neutral-50/80 border-b border-border/60">
                                <button type="button"
                                        @click="toggleItemOpen(index)"
                                        class="flex items-center gap-3 min-w-0 flex-1 text-left group">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-700 text-xs font-bold shrink-0" x-text="index + 1"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-primary truncate" x-text="itemLabel(item, index)"></p>
                                        <p class="text-xs text-secondary mt-0.5"
                                           x-text="item.is_expired
                                              ? 'Offre expirée · Masquée sur le site'
                                              : (item.is_active ? (item.vacancies + ' poste(s) · Visible') : 'Masquée sur le site')"></p>
                                    </div>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-border/70 bg-card text-secondary shrink-0 transition-all duration-200 group-hover:border-primary/30 group-hover:text-primary"
                                          :class="item.open ? 'rotate-180 bg-primary/5 border-primary/20 text-primary' : ''">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </span>
                                </button>

                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" @click="moveItem(index, -1)" :disabled="index === 0"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-secondary hover:text-primary hover:bg-primary/10 transition disabled:opacity-30 disabled:pointer-events-none" title="Monter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    </button>
                                    <button type="button" @click="moveItem(index, 1)" :disabled="index === items.length - 1"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-secondary hover:text-primary hover:bg-primary/10 transition disabled:opacity-30 disabled:pointer-events-none" title="Descendre">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <button type="button" @click="removeItem(index)"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-danger hover:bg-danger/10 transition" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <div x-show="item.open" x-cloak x-transition.opacity.duration.200ms class="p-4 sm:p-5 space-y-4">
                                <label class="inline-flex items-center gap-3 cursor-pointer group pb-4 mb-2 border-b border-border/50 w-full">
                                    <input type="hidden" :name="'content[items][' + index + '][is_active]'" value="0">
                                    <input type="checkbox" :name="'content[items][' + index + '][is_active]'" value="1" x-model="item.is_active"
                                           class="w-5 h-5 rounded-md border-border text-primary focus:ring-primary/30 transition">
                                    <span class="text-sm font-medium text-secondary group-hover:text-primary transition-colors">Afficher cette offre sur le site</span>
                                </label>

                                <label class="inline-flex items-start gap-3 cursor-pointer group pb-4 mb-2 border-b border-border/50 w-full">
                                    <input type="hidden" :name="'content[items][' + index + '][is_expired]'" value="0">
                                    <input type="checkbox"
                                           :name="'content[items][' + index + '][is_expired]'"
                                           value="1"
                                           x-model="item.is_expired"
                                           class="w-5 h-5 mt-0.5 rounded-md border-border text-rose-600 focus:ring-rose-500/30 transition">
                                    <span>
                                        <span class="block text-sm font-medium text-secondary group-hover:text-primary transition-colors">Offre expirée</span>
                                        <span class="block text-xs text-secondary mt-1">Une offre expirée est automatiquement masquée sur le site public.</span>
                                    </span>
                                </label>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Titre</label>
                                        <input type="text" :name="'content[items][' + index + '][title]'" x-model="item.title" class="input-field w-full text-sm" placeholder="Technicien prothésiste dentaire">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Postes vacants</label>
                                        <input type="number" min="0" step="1" :name="'content[items][' + index + '][vacancies]'" x-model.number="item.vacancies" class="input-field w-full text-sm" placeholder="2">
                                        <p class="mt-1 text-xs text-secondary">Nombre de postes ouverts pour cette offre</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Type d’emploi désiré</label>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="type in employmentTypeOptions" :key="'emp-' + index + '-' + type">
                                            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition"
                                                   :class="hasEmploymentType(item, type) ? 'border-primary bg-primary/10 text-primary' : 'border-border bg-card text-secondary hover:border-primary/30'">
                                                <input type="checkbox"
                                                       class="rounded border-border text-primary focus:ring-primary/30"
                                                       :checked="hasEmploymentType(item, type)"
                                                       @change="toggleEmploymentType(item, type)">
                                                <span class="text-sm font-semibold" x-text="type"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <template x-for="(type, typeIndex) in item.employment_types" :key="'emp-hidden-' + index + '-' + typeIndex">
                                        <input type="hidden" :name="'content[items][' + index + '][employment_types][' + typeIndex + ']'" :value="type">
                                    </template>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Expérience</label>
                                        <input type="text" :name="'content[items][' + index + '][experience]'" x-model="item.experience" class="input-field w-full text-sm" placeholder="0 à 1 an">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Niveau d’étude</label>
                                        <input type="text" :name="'content[items][' + index + '][education_level]'" x-model="item.education_level" class="input-field w-full text-sm" placeholder="Licence, Bac + 3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Langue</label>
                                        <input type="text" :name="'content[items][' + index + '][languages]'" x-model="item.languages" class="input-field w-full text-sm" placeholder="Français, Anglais, Arabe">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-secondary uppercase tracking-wide mb-1.5">Genre</label>
                                        <select :name="'content[items][' + index + '][gender]'" x-model="item.gender" class="input-field w-full text-sm">
                                            <template x-for="option in genderOptions" :key="'gender-' + index + '-' + option">
                                                <option :value="option" x-text="option"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                @include('admin.vitrine.partials.recrutement-description-html-editor')
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-border/60">
                    @include('admin.vitrine.partials.btn-add', ['label' => 'Ajouter une offre', 'click' => 'addItem()'])
                </div>
            </div>
        </div>
    </section>
</div>
