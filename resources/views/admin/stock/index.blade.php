<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-primary rounded-lg">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl font-semibold text-primary">{{ __('Stock') }}</h2>
        </div>
    </x-slot>

    <div
        x-data="{
            activeTab: '{{ $activeTab }}',
            showDeleteModal: false,
            deleteFormAction: '',
            deleteLabel: '',
            showEditElementModal: false,
            editingElement: null,
            editElementForm: { nom: '' },
            showEditStockModal: false,
            editingStock: null,
            editStockForm: { element_id: '', qte: '' },
            easyloadElementsPage: {{ $elements->currentPage() }},
            easyloadElementsHasMore: @json($elements->hasMorePages()),
            easyloadElementsLoading: false,
            easyloadElementsObserver: null,
            easyloadStockPage: {{ $stocks->currentPage() }},
            easyloadStockHasMore: @json($stocks->hasMorePages()),
            easyloadStockLoading: false,
            easyloadStockObserver: null,
            initEasyloadStock() {
                if (this.easyloadStockObserver) {
                    this.easyloadStockObserver.disconnect();
                }
                if (!this.$refs.stockEasyloadSentinel || !this.easyloadStockHasMore) return;
                this.easyloadStockObserver = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) {
                        this.loadMoreStock();
                    }
                }, { rootMargin: '120px' });
                this.easyloadStockObserver.observe(this.$refs.stockEasyloadSentinel);
            },
            async loadMoreStock() {
                if (this.easyloadStockLoading || !this.easyloadStockHasMore) return;
                this.easyloadStockLoading = true;
                try {
                    const params = new URLSearchParams(window.location.search);
                    params.set('tab', 'stock');
                    params.set('stock_page', this.easyloadStockPage + 1);
                    const response = await fetch(`{{ route('admin.stock.index') }}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    const tbody = document.getElementById('stocks-tbody');
                    if (tbody && data.html) {
                        const emptyRow = document.getElementById('stocks-empty-row');
                        if (emptyRow) emptyRow.remove();
                        tbody.insertAdjacentHTML('beforeend', data.html);
                        if (window.Alpine) {
                            Alpine.initTree(tbody);
                        }
                    }
                    this.easyloadStockPage++;
                    this.easyloadStockHasMore = data.has_more;
                } catch (e) {
                    console.error('Erreur easyload stock:', e);
                } finally {
                    this.easyloadStockLoading = false;
                    if (this.easyloadStockHasMore) {
                        this.$nextTick(() => this.initEasyloadStock());
                    }
                }
            },
            initEasyloadElements() {
                if (this.easyloadElementsObserver) {
                    this.easyloadElementsObserver.disconnect();
                }
                if (!this.$refs.elementsEasyloadSentinel || !this.easyloadElementsHasMore) return;
                this.easyloadElementsObserver = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) {
                        this.loadMoreElements();
                    }
                }, { rootMargin: '120px' });
                this.easyloadElementsObserver.observe(this.$refs.elementsEasyloadSentinel);
            },
            async loadMoreElements() {
                if (this.easyloadElementsLoading || !this.easyloadElementsHasMore) return;
                this.easyloadElementsLoading = true;
                try {
                    const params = new URLSearchParams(window.location.search);
                    params.set('tab', 'elements');
                    params.set('elements_page', this.easyloadElementsPage + 1);
                    const response = await fetch(`{{ route('admin.stock.index') }}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    const tbody = document.getElementById('elements-tbody');
                    if (tbody && data.html) {
                        const emptyRow = document.getElementById('elements-empty-row');
                        if (emptyRow) emptyRow.remove();
                        tbody.insertAdjacentHTML('beforeend', data.html);
                        if (window.Alpine) {
                            Alpine.initTree(tbody);
                        }
                    }
                    this.easyloadElementsPage++;
                    this.easyloadElementsHasMore = data.has_more;
                } catch (e) {
                    console.error('Erreur easyload éléments:', e);
                } finally {
                    this.easyloadElementsLoading = false;
                    if (this.easyloadElementsHasMore) {
                        this.$nextTick(() => this.initEasyloadElements());
                    }
                }
            }
        }"
        x-cloak
        x-init="$nextTick(() => activeTab === 'elements' ? initEasyloadElements() : initEasyloadStock())"
        class="stock-page"
    >
        <div class="py-4 sm:py-8 bg-app min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    @php
                        $autoHideSuccessMessages = [
                            'Quantité ajoutée au stock existant.',
                            'Stock mis à jour avec succès.',
                        ];
                        $autoHideSuccess = in_array(session('success'), $autoHideSuccessMessages, true);
                    @endphp
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @if($autoHideSuccess)
                        x-init="setTimeout(() => show = false, 5000)"
                        @endif
                        class="mb-4 sm:mb-6 p-3 sm:p-4 bg-accent-secondary/10 border-l-4 border-accent-secondary rounded-lg"
                    >
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

                <div class="card">
                    <div class="flex border-b border-border mb-6">
                        <button
                            type="button"
                            @click="activeTab = 'stock'; $nextTick(() => initEasyloadStock())"
                            :class="activeTab === 'stock' ? 'border-primary text-primary bg-primary/5' : 'border-transparent text-secondary hover:text-primary hover:border-primary/30'"
                            class="px-4 sm:px-6 py-3 text-sm font-medium border-b-2 transition-colors duration-200"
                        >
                            Stock
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'elements'; $nextTick(() => initEasyloadElements())"
                            :class="activeTab === 'elements' ? 'border-primary text-primary bg-primary/5' : 'border-transparent text-secondary hover:text-primary hover:border-primary/30'"
                            class="px-4 sm:px-6 py-3 text-sm font-medium border-b-2 transition-colors duration-200"
                        >
                            Éléments
                        </button>
                    </div>

                    <div x-show="activeTab === 'stock'" x-cloak>
                        @include('admin.stock.partials.tab-stock')
                    </div>

                    <div x-show="activeTab === 'elements'" x-cloak>
                        @include('admin.stock.partials.tab-elements')
                    </div>
                </div>
            </div>
        </div>

        @can('manage_stock')
        {{-- Modal édition élément --}}
        <div x-show="showEditElementModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" @click.away="showEditElementModal = false">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
            <div class="bg-white rounded-2xl w-full sm:max-w-md relative z-10 p-6 border border-gray-100 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-primary mb-4">Modifier l'élément</h3>
                <form x-bind:action="'{{ url('admin/stock/elements') }}/' + editingElement" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Nom d'élément <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="input-field w-full" x-model="editElementForm.nom" required />
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showEditElementModal = false" class="btn-secondary">Annuler</button>
                        <button type="submit" class="btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal édition stock --}}
        <div x-show="showEditStockModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" @click.away="showEditStockModal = false">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
            <div class="bg-white rounded-2xl w-full sm:max-w-md relative z-10 p-6 border border-gray-100 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-primary mb-4">Modifier le stock</h3>
                <form x-bind:action="'{{ url('admin/stock/lines') }}/' + editingStock" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Élément <span class="text-danger">*</span></label>
                        <select name="element_id" class="input-field w-full" x-model="editStockForm.element_id" required>
                            <option value="">Sélectionner un élément</option>
                            @foreach($elementsForSelect as $element)
                                <option value="{{ $element->id }}">{{ $element->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Qté <span class="text-danger">*</span></label>
                        <input type="number" name="qte" min="0" step="1" class="input-field w-full" x-model="editStockForm.qte" required />
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showEditStockModal = false" class="btn-secondary">Annuler</button>
                        <button type="submit" class="btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal suppression --}}
        <div x-show="showDeleteModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" @click.away="showDeleteModal = false">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
            <div class="bg-white rounded-2xl max-w-sm w-full relative z-10 p-6 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-primary mb-2">Confirmer la suppression</h3>
                <p class="text-secondary text-sm mb-6">Supprimer <strong x-text="deleteLabel"></strong> ?</p>
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
