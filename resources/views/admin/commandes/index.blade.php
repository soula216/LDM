@php
    $bulkStatusEnabled = !isset($dentist) && auth()->user()->can('change_commande_status');
    $tableColspan = (auth()->user()->hasRole('dentist') ? 6 : 8) + ($bulkStatusEnabled ? 1 : 0);
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    @if(isset($dentist))
                        {{ __('Commandes') }} – {{ $dentist->full_name ?: $dentist->name }}
                    @else
                        {{ __('Liste des Commandes') }}
                    @endif
                </h2>
            </div>
            @can('create_commandes')
            @if(!isset($dentist))
            <a href="{{ route('admin.commandes.create') }}" class="btn-primary inline-flex items-center w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden sm:inline">Nouvelle Commande</span>
                <span class="sm:hidden">Nouveau</span>
            </a>
            @endif
            @endcan
        </div>
    </x-slot>

    <div
        x-data="{
            showDeleteModal: false,
            commandeToDelete: null,
            deleteFormAction: '',
            @if($bulkStatusEnabled)
            selectedCommandeIds: [],
            selectAllChecked: false,
            selectAllIndeterminate: false,
            bulkStatus: '',
            toggleCommandeSelection(id, checked) {
                if (checked) {
                    if (!this.selectedCommandeIds.includes(id)) {
                        this.selectedCommandeIds.push(id);
                    }
                } else {
                    this.selectedCommandeIds = this.selectedCommandeIds.filter(i => i !== id);
                }
                this.syncSelectAllState();
            },
            syncSelectAllState() {
                const checkboxes = document.querySelectorAll('#commandes-tbody .commande-row-checkbox');
                const total = checkboxes.length;
                const checked = Array.from(checkboxes).filter(cb => cb.checked).length;
                this.selectAllChecked = total > 0 && checked === total;
                this.selectAllIndeterminate = checked > 0 && checked < total;
            },
            toggleSelectAll(checked) {
                const checkboxes = document.querySelectorAll('#commandes-tbody .commande-row-checkbox');
                this.selectedCommandeIds = [];
                checkboxes.forEach(cb => {
                    cb.checked = checked;
                    if (checked) {
                        const id = parseInt(cb.value, 10);
                        if (!this.selectedCommandeIds.includes(id)) {
                            this.selectedCommandeIds.push(id);
                        }
                    }
                });
                this.selectAllChecked = checked && checkboxes.length > 0;
                this.selectAllIndeterminate = false;
            },
            clearSelection() {
                this.selectedCommandeIds = [];
                this.selectAllChecked = false;
                this.selectAllIndeterminate = false;
                this.bulkStatus = '';
                document.querySelectorAll('#commandes-tbody .commande-row-checkbox').forEach(cb => {
                    cb.checked = false;
                });
            },
            submitBulkStatus() {
                if (this.selectedCommandeIds.length === 0 || !this.bulkStatus) {
                    return;
                }
                this.$refs.bulkStatusForm.submit();
            },
            @endif
            @unless(isset($dentist))
            easyloadPage: {{ $commandes->currentPage() }},
            easyloadHasMore: @json($commandes->hasMorePages()),
            easyloadLoading: false,
            easyloadObserver: null,
            initEasyload() {
                if (this.easyloadObserver) {
                    this.easyloadObserver.disconnect();
                }
                if (!this.$refs.easyloadSentinel || !this.easyloadHasMore) return;
                this.easyloadObserver = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) {
                        this.loadMoreCommandes();
                    }
                }, { rootMargin: '200px' });
                this.easyloadObserver.observe(this.$refs.easyloadSentinel);
            },
            async loadMoreCommandes() {
                if (this.easyloadLoading || !this.easyloadHasMore) return;
                this.easyloadLoading = true;
                try {
                    const params = new URLSearchParams(window.location.search);
                    params.set('page', this.easyloadPage + 1);
                    const response = await fetch(`{{ route('admin.commandes.index') }}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    const tbody = document.getElementById('commandes-tbody');
                    if (tbody && data.html) {
                        const emptyRow = document.getElementById('commandes-empty-row');
                        if (emptyRow) emptyRow.remove();
                        tbody.insertAdjacentHTML('beforeend', data.html);
                        if (window.Alpine) {
                            Alpine.initTree(tbody);
                        }
                        @if($bulkStatusEnabled)
                        this.syncSelectAllState();
                        @endif
                    }
                    this.easyloadPage++;
                    this.easyloadHasMore = data.has_more;
                } catch (error) {
                    console.error('Erreur easyload commandes:', error);
                } finally {
                    this.easyloadLoading = false;
                    if (this.easyloadHasMore) {
                        this.$nextTick(() => this.initEasyload());
                    } else if (this.easyloadObserver) {
                        this.easyloadObserver.disconnect();
                    }
                }
            }
            @endunless
        }"
        x-cloak
        @unless(isset($dentist))
        x-init="$nextTick(() => initEasyload())"
        @endunless
        class="commandes-page"
    >
    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card">
                @if(session('success'))
                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-accent-secondary/10 border-l-4 border-accent-secondary rounded-lg flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-accent-secondary mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-accent-secondary font-medium text-sm sm:text-base">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-danger/10 border-l-4 border-danger rounded-lg flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-danger mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-danger font-medium text-sm sm:text-base">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Barre de filtre (GET : filtrage côté backend) -->
                <form method="GET" action="{{ isset($dentist) ? route('admin.dentists.commandes.index', $dentist) : route('admin.commandes.index') }}" class="mb-4 sm:mb-6">
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="w-full max-w-xs">
                            <input 
                                type="text" 
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Filter par dentiste ou par patient"
                                class="block w-full px-3 py-2 sm:py-3 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base input-field h-10 sm:h-11"
                            />
                        </div>
                        <div class="w-full min-w-[140px] max-w-[180px]">
                            <x-label for="filter-date-debut" value="{{ __('Date début') }}" class="text-primary font-medium mb-2" />
                            <input 
                                id="filter-date-debut"
                                type="date" 
                                name="date_debut"
                                value="{{ request('date_debut') }}"
                                x-on:click="if ($el.showPicker) $el.showPicker()"
                                class="block w-full input-field cursor-pointer"
                            />
                        </div>
                        <div class="w-full min-w-[140px] max-w-[180px]">
                            <x-label for="filter-date-fin" value="{{ __('Date fin') }}" class="text-primary font-medium mb-2" />
                            <input 
                                id="filter-date-fin"
                                type="date" 
                                name="date_fin"
                                value="{{ request('date_fin') }}"
                                x-on:click="if ($el.showPicker) $el.showPicker()"
                                class="block w-full input-field cursor-pointer"
                            />
                        </div>
                        <div class="w-full min-w-[140px] max-w-[180px]">
                            <x-label for="filter-status" value="{{ __('Statut') }}" class="text-primary font-medium mb-2" />
                            <select 
                                id="filter-status"
                                name="status"
                                class="block w-full input-field h-10 sm:h-11"
                            >
                                <option value="">Tous les statuts</option>
                                @foreach(\App\Enums\CommandeStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-primary h-10 sm:h-11 px-4">
                            Filtrer
                        </button>
                        <a href="{{ isset($dentist) ? route('admin.dentists.commandes.index', $dentist) : route('admin.commandes.index') }}" class="inline-flex items-center justify-center h-10 sm:h-11 px-4 rounded-lg border border-border bg-card text-secondary hover:bg-neutral-100 font-medium text-sm transition-colors duration-200">
                            Réinitialiser
                        </a>
                    </div>
                </form>

                @if($bulkStatusEnabled)
                <form x-ref="bulkStatusForm" method="POST" action="{{ route('admin.commandes.bulk-status') }}" class="hidden">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" x-bind:value="bulkStatus">
                    <template x-for="id in selectedCommandeIds" :key="id">
                        <input type="hidden" name="commande_ids[]" :value="id">
                    </template>
                </form>

                <div
                    x-show="selectedCommandeIds.length > 0"
                    x-cloak
                    class="mb-4 sm:mb-6 p-3 sm:p-4 bg-primary/5 border border-primary/20 rounded-lg flex flex-col sm:flex-row sm:items-center gap-3"
                >
                    <p class="text-sm font-medium text-primary">
                        <span x-text="selectedCommandeIds.length"></span> commande(s) sélectionnée(s)
                    </p>
                    <div class="flex flex-wrap items-center gap-3 sm:ml-auto">
                        <select x-model="bulkStatus" class="input-field h-10 sm:h-11 min-w-[160px]">
                            <option value="">Choisir un statut</option>
                            @foreach(\App\Enums\CommandeStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <button
                            type="button"
                            @click="submitBulkStatus()"
                            :disabled="!bulkStatus"
                            class="btn-primary h-10 sm:h-11 px-4 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Appliquer le statut
                        </button>
                        <button
                            type="button"
                            @click="clearSelection()"
                            class="inline-flex items-center justify-center h-10 sm:h-11 px-4 rounded-lg border border-border bg-card text-secondary hover:bg-neutral-100 font-medium text-sm transition-colors duration-200"
                        >
                            Annuler la sélection
                        </button>
                    </div>
                </div>
                @endif

                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                @if($bulkStatusEnabled)
                                <th class="px-3 sm:px-6 py-3 w-10">
                                    <input
                                        type="checkbox"
                                        class="rounded border-border text-primary focus:ring-primary"
                                        :checked="selectAllChecked"
                                        :indeterminate.prop="selectAllIndeterminate"
                                        @change="toggleSelectAll($event.target.checked)"
                                        title="Tout sélectionner (lignes chargées)"
                                    />
                                </th>
                                @endif
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Numéro</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Patient</th>
                                @unless(auth()->user()->hasRole('dentist') || isset($dentist))
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden md:table-cell">Dentiste</th>
                                @endunless
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Statut</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden lg:table-cell">Urgent</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden lg:table-cell">Créé par</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden md:table-cell">Date</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="commandes-tbody" class="bg-card divide-y divide-border">
                            @if(!isset($dentist))
                                @if($commandes->isEmpty())
                                    <tr id="commandes-empty-row">
                                        <td colspan="{{ $tableColspan }}" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-secondary text-base font-medium">Aucune commande trouvée</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @include('admin.commandes.partials.rows', ['commandes' => $commandes, 'bulkSelect' => true])
                                @endif
                            @else
                                @if($commandes->isEmpty())
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-secondary text-base font-medium">Aucune commande trouvée</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @include('admin.commandes.partials.rows', compact('commandes'))
                                @endif
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(!isset($dentist))
                <div class="px-4 sm:px-6 py-4 border-t border-border">
                    <p class="text-sm text-secondary text-center" x-show="!easyloadHasMore && !easyloadLoading">
                        {{ $commandes->total() }} commande(s) au total
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
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div x-show="showDeleteModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="showDeleteModal = false">
        <div x-on:click.stop class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
        <div class="bg-white rounded-2xl max-w-sm sm:max-w-md w-full relative z-10 overflow-hidden border border-gray-100" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            <div class="px-4 py-5 sm:px-6 sm:py-6">
                <div class="flex items-center mb-4 gap-3 sm:gap-3">
                    <div class="flex-shrink-0 bg-red-500/10 rounded-full p-2">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-primary">Confirmation de suppression</h3>
                </div>
                <p class="text-secondary text-sm sm:text-base mb-6">
                    Êtes-vous sûr de vouloir supprimer la commande <strong x-text="commandeToDelete"></strong> ? Cette action est irréversible.
                </p>
                <form :action="deleteFormAction" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <div class="border-t border-border pt-4 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showDeleteModal = false" 
                            class="px-4 py-2 text-sm sm:text-base font-medium text-secondary bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors duration-200"
                        >
                            Annuler
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-sm sm:text-base font-medium text-white bg-danger hover:bg-red-700 rounded-lg transition-colors duration-200 shadow-sm"
                        >
                            Supprimer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>