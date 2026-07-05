<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-accent-secondary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Gestion des services') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div
        x-data="{
            showDeleteModal: false,
            serviceToDelete: null,
            deleteFormAction: '',
            showEditModal: false,
            editingService: null,
            editForm: {
                nom: '',
                prix_unitaire_ttc: '',
                groupe_id: ''
            },
            easyloadPage: {{ $services->currentPage() }},
            easyloadHasMore: @json($services->hasMorePages()),
            easyloadLoading: false,
            easyloadObserver: null,
            initEasyload() {
                if (this.easyloadObserver) {
                    this.easyloadObserver.disconnect();
                }
                if (!this.$refs.easyloadSentinel || !this.easyloadHasMore) return;
                this.easyloadObserver = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) {
                        this.loadMoreServices();
                    }
                }, { rootMargin: '200px' });
                this.easyloadObserver.observe(this.$refs.easyloadSentinel);
            },
            async loadMoreServices() {
                if (this.easyloadLoading || !this.easyloadHasMore) return;
                this.easyloadLoading = true;
                try {
                    const params = new URLSearchParams(window.location.search);
                    params.set('page', this.easyloadPage + 1);
                    const response = await fetch(`{{ route('admin.services.index') }}?${params}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    const tbody = document.getElementById('services-tbody');
                    if (tbody && data.html) {
                        const emptyRow = document.getElementById('services-empty-row');
                        if (emptyRow) emptyRow.remove();
                        tbody.insertAdjacentHTML('beforeend', data.html);
                        if (window.Alpine) {
                            Alpine.initTree(tbody);
                        }
                    }
                    this.easyloadPage++;
                    this.easyloadHasMore = data.has_more;
                } catch (error) {
                    console.error('Erreur easyload services:', error);
                } finally {
                    this.easyloadLoading = false;
                    if (this.easyloadHasMore) {
                        this.$nextTick(() => this.initEasyload());
                    } else if (this.easyloadObserver) {
                        this.easyloadObserver.disconnect();
                    }
                }
            }
        }"
        x-cloak
        x-init="$nextTick(() => initEasyload())"
        class="services-page"
    >
    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

            @if ($errors->any())
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-danger/10 border-l-4 border-danger rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-danger mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-danger font-medium text-sm sm:text-base">Erreurs de validation</span>
                    </div>
                    <ul class="list-disc list-inside text-danger text-sm sm:text-base space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Add Service Form -->
            <div class="card mb-6">
                <div class="mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-primary flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Ajouter un Service
                    </h3>
                </div>
                <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-4 sm:space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <div class="sm:col-span-1">
                            <x-label for="nom" value="{{ __('Nom du Service') }}" class="text-primary font-medium mb-2" />
                            <x-input name="nom" type="text" class="input-field" required />
                            <x-input-error for="nom" class="mt-2" />
                        </div>
                        <div class="sm:col-span-1">
                            <x-label for="prix_unitaire_ttc" value="{{ __('Prix TTC (TND)') }}" class="text-primary font-medium mb-2" />
                            <x-input name="prix_unitaire_ttc" type="number" step="0.01" min="0" class="input-field" required />
                            <x-input-error for="prix_unitaire_ttc" class="mt-2" />
                        </div>
                        <div class="sm:col-span-1">
                            <x-label for="groupe_id" value="{{ __('Groupe') }}" class="text-primary font-medium mb-2" />
                            <select name="groupe_id" id="groupe_id" class="input-field">
                                <option value="">Sélectionner un groupe</option>
                                @foreach($groupes as $groupe)
                                    <option value="{{ $groupe->id }}">{{ $groupe->nom }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="groupe_id" class="mt-2" />
                        </div>
                        <div class="sm:col-span-1 flex items-end">
                            <button type="submit" class="btn-primary w-full sm:w-auto">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Ajouter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Services Table -->
            <div class="card">
                <div class="mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-primary flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Liste des Services
                    </h3>
                </div>
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Service</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Prix TTC (TND)</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Groupe</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="services-tbody" class="bg-card divide-y divide-border">
                            @if($services->isEmpty())
                                <tr id="services-empty-row">
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-secondary text-base font-medium">Aucun service</p>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @include('admin.services.partials.rows', compact('services'))
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 py-4 border-t border-border">
                    <p class="text-sm text-secondary text-center" x-show="!easyloadHasMore && !easyloadLoading">
                        {{ $services->total() }} service(s) au total
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

            <!-- Link to Dentist Pricing -->
            <div class="mt-6">
                <a href="{{ route('admin.pricing.dentists.index') }}" class="inline-flex items-center text-primary hover:text-primary/80 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Gestion des Prix par Dentiste
                </a>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de service -->
    <div x-show="showEditModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="showEditModal = false">
        <div x-on:click.stop class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
        <div class="bg-white rounded-2xl w-full sm:max-w-md relative z-10 overflow-hidden border border-gray-100 mx-auto" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            <div class="px-4 py-5 sm:px-6 sm:py-6">
                <div class="flex items-center mb-4 gap-3 sm:gap-3">
                    <div class="flex-shrink-0 bg-primary/10 rounded-full p-2">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-primary">Modifier le service</h3>
                </div>
                <form x-bind:action="'{{ url('admin/services') }}/' + editingService" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                                    <div>
                                        <x-label for="edit_nom" value="{{ __('Nom du Service') }}" class="text-primary font-medium mb-2" />
                                        <x-input 
                                            id="edit_nom" 
                                            name="nom" 
                                            type="text" 
                                            class="input-field" 
                                            x-model="editForm.nom" 
                                            required
                                        />
                                        <x-input-error for="nom" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-label for="edit_prix_unitaire_ttc" value="{{ __('Prix TTC (TND)') }}" class="text-primary font-medium mb-2" />
                                        <x-input 
                                            id="edit_prix_unitaire_ttc" 
                                            name="prix_unitaire_ttc" 
                                            type="number" 
                                            step="0.01" 
                                            min="0" 
                                            class="input-field" 
                                            x-model="editForm.prix_unitaire_ttc" 
                                            required
                                        />
                                        <x-input-error for="prix_unitaire_ttc" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-label for="edit_groupe_id" value="{{ __('Groupe') }}" class="text-primary font-medium mb-2" />
                                        <select 
                                            id="edit_groupe_id" 
                                            name="groupe_id" 
                                            class="input-field" 
                                            x-model="editForm.groupe_id"
                                        >
                                            <option value="">Sélectionner un groupe</option>
                                            @foreach($groupes as $groupe)
                                                <option value="{{ $groupe->id }}">{{ $groupe->nom }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error for="groupe_id" class="mt-2" />
                                    </div>
                    <div class="border-t border-border pt-4 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showEditModal = false" 
                            class="px-4 py-2 text-sm sm:text-base font-medium text-secondary bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors duration-200"
                        >
                            Annuler
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-sm sm:text-base font-medium text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors duration-200 shadow-sm"
                        >
                            Enregistrer
                        </button>
                    </div>
                </form>
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
                    Êtes-vous sûr de vouloir supprimer le service <strong x-text="serviceToDelete"></strong> ? Cette action est irréversible.
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