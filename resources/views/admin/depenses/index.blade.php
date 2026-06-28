<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-warning rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">{{ __('Gestion de dépenses') }}</h2>
            </div>
            <div class="px-4 py-2 bg-primary/10 border border-primary/20 rounded-xl">
                <p class="text-xs text-secondary uppercase tracking-wide font-medium">Total</p>
                <p class="text-lg font-bold text-primary">{{ number_format($totalMontant, 2, ',', ' ') }} TND</p>
            </div>
        </div>
    </x-slot>

    <div
        x-data="{
            showDeleteModal: false,
            depenseToDelete: null,
            deleteFormAction: '',
            showEditModal: false,
            editingDepense: null,
            editForm: { nom: '', qte: '', date: '', montant: '' },
            easyloadPage: {{ $depenses->currentPage() }},
            easyloadHasMore: @json($depenses->hasMorePages()),
            easyloadLoading: false,
            easyloadObserver: null,
            initEasyload() {
                if (!this.$refs.easyloadSentinel) return;
                this.easyloadObserver = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) {
                        this.loadMore();
                    }
                }, { rootMargin: '120px' });
                this.easyloadObserver.observe(this.$refs.easyloadSentinel);
            },
            async loadMore() {
                if (this.easyloadLoading || !this.easyloadHasMore) return;
                this.easyloadLoading = true;
                try {
                    const params = new URLSearchParams(window.location.search);
                    params.set('page', this.easyloadPage + 1);
                    const response = await fetch(`{{ route('admin.depenses.index') }}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    const tbody = document.getElementById('depenses-tbody');
                    if (tbody && data.html) {
                        const emptyRow = document.getElementById('depenses-empty-row');
                        if (emptyRow) emptyRow.remove();
                        tbody.insertAdjacentHTML('beforeend', data.html);
                        if (window.Alpine) {
                            Alpine.initTree(tbody);
                        }
                    }
                    this.easyloadPage++;
                    this.easyloadHasMore = data.has_more;
                } catch (e) {
                    console.error('Erreur easyload dépenses:', e);
                } finally {
                    this.easyloadLoading = false;
                }
            }
        }"
        x-cloak
        x-init="initEasyload()"
        class="depenses-page"
    >
        <div class="py-4 sm:py-8 bg-app min-h-screen">
            <div class="w-full max-w-none mx-auto px-4 sm:px-6 lg:px-10">
                @if(session('success'))
                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-accent-secondary/10 border-l-4 border-accent-secondary rounded-lg flex items-center">
                        <span class="text-accent-secondary font-medium text-sm sm:text-base">{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-danger/10 border-l-4 border-danger rounded-lg">
                        <ul class="list-disc list-inside text-danger text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @can('manage_depenses')
                <div class="card mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-primary mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Ajouter une dépense
                    </h3>
                    <form action="{{ route('admin.depenses.store') }}" method="POST">
                        @csrf
                        <div class="depenses-form-row">
                            <div class="depenses-field-nom">
                                <label for="nom" class="block text-sm font-medium text-primary mb-2 whitespace-nowrap">Nom de dépense <span class="text-danger">*</span></label>
                                <input type="text" name="nom" id="nom" value="{{ old('nom') }}" class="input-field" placeholder="Ex : Achat matériel" required />
                            </div>
                            <div class="depenses-field-qte">
                                <label for="qte" class="block text-sm font-medium text-primary mb-2 whitespace-nowrap">Qté <span class="text-secondary text-xs font-normal">(opt.)</span></label>
                                <input type="number" name="qte" id="qte" value="{{ old('qte') }}" min="1" step="1" class="input-field" placeholder="5" />
                            </div>
                            <div class="depenses-field-date">
                                <label for="date" class="block text-sm font-medium text-primary mb-2 whitespace-nowrap">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="input-field" required />
                            </div>
                            <div class="depenses-field-montant">
                                <label for="montant" class="block text-sm font-medium text-primary mb-2 whitespace-nowrap">Montant <span class="text-danger">*</span></label>
                                <div class="relative">
                                    <input type="number" name="montant" id="montant" value="{{ old('montant') }}" step="0.01" min="0" class="input-field pr-14" placeholder="0.00" required />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-sm font-semibold text-secondary">TND</span>
                                    </div>
                                </div>
                            </div>
                            <div class="depenses-field-submit">
                                <button type="submit" class="btn-primary whitespace-nowrap">Ajouter</button>
                            </div>
                        </div>
                    </form>
                </div>
                @endcan

                <div class="card">
                    <h3 class="text-base sm:text-lg font-semibold text-primary mb-4">Liste des dépenses</h3>

                    <form method="GET" action="{{ route('admin.depenses.index') }}" class="mb-4 sm:mb-6">
                        <div class="flex flex-wrap items-end gap-3">
                            <div class="w-full max-w-xs">
                                <label for="filter-nom" class="block text-sm font-medium text-primary mb-2">Nom de dépense</label>
                                <input
                                    type="text"
                                    id="filter-nom"
                                    name="nom"
                                    value="{{ request('nom') }}"
                                    placeholder="Rechercher par nom..."
                                    class="input-field w-full"
                                />
                            </div>
                            <div class="w-full min-w-[140px] max-w-[180px]">
                                <label for="filter-date-debut" class="block text-sm font-medium text-primary mb-2">Date début</label>
                                <input
                                    id="filter-date-debut"
                                    type="date"
                                    name="date_debut"
                                    value="{{ request('date_debut') }}"
                                    x-on:click="if ($el.showPicker) $el.showPicker()"
                                    class="input-field w-full cursor-pointer"
                                />
                            </div>
                            <div class="w-full min-w-[140px] max-w-[180px]">
                                <label for="filter-date-fin" class="block text-sm font-medium text-primary mb-2">Date fin</label>
                                <input
                                    id="filter-date-fin"
                                    type="date"
                                    name="date_fin"
                                    value="{{ request('date_fin') }}"
                                    x-on:click="if ($el.showPicker) $el.showPicker()"
                                    class="input-field w-full cursor-pointer"
                                />
                            </div>
                            <button type="submit" class="btn-primary h-10 sm:h-11 px-4">Filtrer</button>
                            <a href="{{ route('admin.depenses.index') }}" class="inline-flex items-center justify-center h-10 sm:h-11 px-4 rounded-lg border border-border bg-card text-secondary hover:bg-neutral-100 font-medium text-sm transition-colors duration-200">
                                Réinitialiser
                            </a>
                        </div>
                    </form>

                    <div>
                        <table class="min-w-full divide-y divide-border">
                            <thead class="bg-neutral-100">
                                <tr>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Nom</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Qté</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Date</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Montant</th>
                                    @can('manage_depenses')
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody id="depenses-tbody" class="bg-card divide-y divide-border">
                                @if($depenses->isEmpty())
                                    <tr id="depenses-empty-row">
                                        <td colspan="{{ auth()->user()->can('manage_depenses') ? 5 : 4 }}" class="px-6 py-12 text-center text-secondary">
                                        {{ request()->hasAny(['nom', 'date_debut', 'date_fin']) ? 'Aucune dépense trouvée pour ces critères' : 'Aucune dépense enregistrée' }}
                                    </td>
                                    </tr>
                                @else
                                    @include('admin.depenses.partials.rows', compact('depenses'))
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 sm:px-6 py-4 border-t border-border">
                        <p class="text-sm text-secondary text-center" x-show="!easyloadHasMore && !easyloadLoading">
                            {{ $depenses->total() }} dépense(s) au total — affichage par 20
                        </p>
                        <div x-ref="easyloadSentinel" class="py-4 flex justify-center min-h-[3rem]">
                            <div x-show="easyloadLoading" class="flex items-center gap-2 text-secondary text-sm">
                                <svg class="animate-spin h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Chargement...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @can('manage_depenses')
        {{-- Modal édition --}}
        <div x-show="showEditModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" @click.away="showEditModal = false">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
            <div class="bg-white rounded-2xl w-full sm:max-w-md relative z-10 p-6 border border-gray-100 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-primary mb-4">Modifier la dépense</h3>
                <form x-bind:action="'{{ url('admin/depenses') }}/' + editingDepense" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Nom de dépense <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="input-field w-full" x-model="editForm.nom" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Qté <span class="text-secondary text-xs font-normal">(optionnel)</span></label>
                        <input type="number" name="qte" min="1" step="1" class="input-field w-full" x-model="editForm.qte" placeholder="Ex : 5" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="input-field w-full" x-model="editForm.date" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Montant <span class="text-danger">*</span></label>
                        <div class="relative">
                            <input type="number" name="montant" step="0.01" min="0" class="input-field w-full pr-14" x-model="editForm.montant" required />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-sm font-semibold text-secondary">TND</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showEditModal = false" class="btn-secondary">Annuler</button>
                        <button type="submit" class="btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal suppression --}}
        <div x-show="showDeleteModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" @click.away="showDeleteModal = false">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
            <div class="bg-white rounded-2xl max-w-sm w-full relative z-10 p-6 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-primary mb-2">Supprimer la dépense</h3>
                <p class="text-secondary text-sm mb-6">Confirmer la suppression de <strong x-text="depenseToDelete"></strong> ?</p>
                <form :action="deleteFormAction" method="POST" class="flex justify-end gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false" class="btn-secondary">Annuler</button>
                    <button type="submit" class="btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
        @endcan
    </div>
</x-app-layout>

@push('styles')
<style>
    .app-main:has(.depenses-page) header > div {
        max-width: none;
        width: 100%;
        padding-left: 2.5rem;
        padding-right: 2.5rem;
    }
</style>
@endpush
