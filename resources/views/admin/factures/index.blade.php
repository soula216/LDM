<div x-data="{ 
    showCreateModal: false,
    showEditModal: false,
    showDeleteModal: false,
    factureToDelete: null,
    deleteFormAction: '',
    editingFactureId: null,
    selectedDentist: '',
    titreDocument: 'bon_livraison',
    factureDate: '{{ now()->format('Y-m-d') }}',
    numFacture: '',
    ancienSolde: '',
    avance: '',
    factureStatus: 'pending',
    montantPaye: '',
    bonsLivraison: [],
    selectedBLs: [],
    loading: false,
    async loadBonsLivraison() {
        if (!this.selectedDentist) {
            this.bonsLivraison = [];
            return;
        }
        this.loading = true;
        try {
            const url = this.editingFactureId 
                ? `{{ route('admin.factures.get-bons-livraison') }}?dentist_id=${this.selectedDentist}&facture_id=${this.editingFactureId}`
                : `{{ route('admin.factures.get-bons-livraison') }}?dentist_id=${this.selectedDentist}`;
            const response = await fetch(url);
            const data = await response.json();
            if (data.success) {
                this.bonsLivraison = data.bonsLivraison;
                // Pré-sélectionner les BL si on est en mode édition
                if (this.editingFactureId) {
                    this.bonsLivraison.forEach(bl => {
                        if (bl.is_selected && !this.selectedBLs.includes(bl.id)) {
                            this.selectedBLs.push(bl.id);
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            this.loading = false;
        }
    },
    openEditModal(facture) {
        this.editingFactureId = facture.id;
        this.selectedDentist = facture.dentist_id;
        this.titreDocument = facture.titre_document || 'bon_livraison';
        this.factureDate = facture.date;
        this.numFacture = facture.num_facture;
        this.ancienSolde = facture.ancien_solde ?? '';
        this.avance = facture.avance ?? '';
        this.factureStatus = facture.status || 'pending';
        this.montantPaye = facture.montant_paye || '';
        this.selectedBLs = facture.bons_livraison_ids || [];
        this.showEditModal = true;
        this.loadBonsLivraison();
    },
    closeEditModal() {
        this.showEditModal = false;
        this.editingFactureId = null;
        this.selectedDentist = '';
        this.titreDocument = 'bon_livraison';
        this.factureDate = '{{ now()->format('Y-m-d') }}';
        this.numFacture = '';
        this.ancienSolde = '';
        this.avance = '';
        this.factureStatus = 'pending';
        this.montantPaye = '';
        this.selectedBLs = [];
        this.bonsLivraison = [];
    },
    openDeleteModal(factureId, factureNum) {
        this.factureToDelete = factureNum;
        this.deleteFormAction = '{{ url('admin/factures') }}/' + factureId;
        this.showDeleteModal = true;
    }
}" x-cloak>
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
                    {{ __('Liste des Factures') }}
                </h2>
            </div>
            @can('create_factures')
            <button 
                @click="showCreateModal = true; titreDocument = 'bon_livraison'; ancienSolde = ''; avance = ''"
                class="btn-primary inline-flex items-center w-full sm:w-auto justify-center"
            >
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden sm:inline">Créer une facture</span>
                <span class="sm:hidden">Nouveau</span>
            </button>
            @endcan
        </div>
    </x-slot>

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

                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Numéro</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Dentiste</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Date</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Montant</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Montant payé</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Montant restant</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Statut</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-card divide-y divide-border">
                            @forelse($factures as $facture)
                                <tr class="hover:bg-neutral-100/50 transition-colors">
                                    <td class="px-3 sm:px-6 py-4">
                                        <span class="text-sm font-semibold text-primary">{{ $facture->num_facture }}</span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm text-secondary">{{ $facture->dentist->full_name ?? $facture->dentist->name }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm text-secondary">{{ $facture->date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm font-medium text-primary">{{ number_format($facture->montant, 2, ',', ' ') }} TND</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm font-medium text-primary">
                                            @if($facture->montant_paye)
                                                {{ number_format($facture->montant_paye, 2, ',', ' ') }} TND
                                            @else
                                                <span class="text-secondary">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm font-medium text-primary">
                                            @if($facture->montant_restant !== null)
                                                {{ number_format($facture->montant_restant, 2, ',', ' ') }} TND
                                            @else
                                                <span class="text-secondary">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($facture->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($facture->status === 'delivered') bg-blue-100 text-blue-800
                                            @elseif($facture->status === 'paid') bg-green-100 text-green-800
                                            @elseif($facture->status === 'partially_paid') bg-orange-100 text-orange-800
                                            @elseif($facture->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $facture->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.factures.show', $facture) }}" class="text-primary hover:text-primary/80 transition-colors" title="Voir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @can('edit_factures')
                                            <button @click="openEditModal({
                                                id: {{ $facture->id }},
                                                dentist_id: {{ $facture->dentist_id }},
                                                titre_document: '{{ $facture->titre_document ?? 'bon_livraison' }}',
                                                date: '{{ $facture->date->format('Y-m-d') }}',
                                                num_facture: '{{ addslashes($facture->num_facture) }}',
                                                ancien_solde: {{ $facture->ancien_solde ?? 0 }},
                                                avance: {{ $facture->avance ?? 0 }},
                                                status: '{{ $facture->status }}',
                                                montant_paye: {{ $facture->montant_paye ?? 'null' }},
                                                bons_livraison_ids: [{{ $facture->bonsLivraison->pluck('id')->implode(',') }}]
                                            })" class="text-warning hover:text-warning/80 transition-colors" title="Modifier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            @endcan
                                            @can('delete_factures')
                                            <button @click="openDeleteModal({{ $facture->id }}, '{{ $facture->num_facture }}')" class="text-danger hover:text-danger/80 transition-colors" title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-secondary text-base font-medium">Aucune facture trouvée</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($factures->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 px-4 sm:px-6 py-4 border-t border-border">
                    <p class="text-sm text-secondary">Affichage de {{ $factures->firstItem() }} à {{ $factures->lastItem() }} sur {{ $factures->total() }} résultats</p>
                    {{ $factures->onEachSide(2)->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de création de facture -->
    <div x-show="showCreateModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="showCreateModal = false">
        <div x-on:click.stop class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
        <div class="bg-white rounded-2xl max-w-4xl w-full relative z-10 overflow-hidden border border-gray-100 max-h-[90vh] overflow-y-auto" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            <div class="px-4 py-5 sm:px-6 sm:py-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg sm:text-xl font-semibold text-primary">Créer une facture</h3>
                    <button @click="showCreateModal = false" class="text-secondary hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.factures.store') }}" method="POST" id="factureForm">
                    @csrf
                    
                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="titre_document" class="block text-sm font-medium text-primary mb-2">Titre du document</label>
                            <select 
                                name="titre_document" 
                                id="titre_document" 
                                x-model="titreDocument"
                                class="block w-full input-field"
                                required
                            >
                                @foreach(\App\Models\Facture::getTitreDocumentOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="dentist_id" class="block text-sm font-medium text-primary mb-2">Dentiste</label>
                            <select 
                                name="dentist_id" 
                                id="dentist_id" 
                                x-model="selectedDentist"
                                @change="loadBonsLivraison(); selectedBLs = []"
                                class="block w-full input-field"
                                required
                            >
                                <option value="">Sélectionner un dentiste</option>
                                @foreach($dentists as $dentist)
                                    <option value="{{ $dentist->id }}">{{ $dentist->full_name ?: $dentist->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="date" class="block text-sm font-medium text-primary mb-2">Date de facturation</label>
                                <input 
                                    type="date" 
                                    name="date" 
                                    id="date" 
                                    x-model="factureDate"
                                    class="block w-full input-field"
                                    required
                                >
                            </div>

                            <div>
                                <label for="num_facture" class="block text-sm font-medium text-primary mb-2">Numéro de facture</label>
                                <input 
                                    type="text" 
                                    name="num_facture" 
                                    id="num_facture" 
                                    x-model="numFacture"
                                    class="block w-full input-field"
                                    required
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ancien_solde" class="block text-sm font-medium text-primary mb-2">Ancien Solde</label>
                                <input 
                                    type="number" 
                                    step="0.01"
                                    name="ancien_solde" 
                                    id="ancien_solde" 
                                    x-model="ancienSolde"
                                    class="block w-full input-field"
                                    placeholder="0.00"
                                >
                            </div>
                            <div>
                                <label for="avance" class="block text-sm font-medium text-primary mb-2">Avance</label>
                                <input 
                                    type="number" 
                                    step="0.01"
                                    name="avance" 
                                    id="avance" 
                                    x-model="avance"
                                    class="block w-full input-field"
                                    placeholder="0.00"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-primary mb-2">Statut</label>
                            <select 
                                name="status" 
                                id="status" 
                                x-model="factureStatus"
                                class="block w-full input-field"
                                required
                            >
                                @foreach(\App\Models\Facture::getStatuses() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Champs conditionnels pour partially_paid -->
                        <template x-if="factureStatus === 'partially_paid'">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="montant_paye" class="block text-sm font-medium text-primary mb-2">Montant payé</label>
                                    <input 
                                        type="number" 
                                        step="0.01"
                                        name="montant_paye" 
                                        id="montant_paye" 
                                        x-model="montantPaye"
                                        class="block w-full input-field"
                                        :required="factureStatus === 'partially_paid'"
                                    >
                                </div>
                                <div>
                                    <label for="montant_restant" class="block text-sm font-medium text-primary mb-2">Montant restant</label>
                                    <input 
                                        type="text" 
                                        id="montant_restant" 
                                        :value="bonsLivraison.length > 0 ? Math.max(0, (bonsLivraison.filter(bl => selectedBLs.includes(bl.id)).reduce((sum, bl) => sum + parseFloat(bl.total_ttc), 0) + parseFloat(ancienSolde || 0) - parseFloat(avance || 0) - parseFloat(montantPaye || 0))).toFixed(2) : '0.00'"
                                        class="block w-full input-field bg-neutral-100"
                                        readonly
                                    >
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Liste des BL -->
                    <div x-show="selectedDentist" style="display: none;">
                        <h4 class="text-md font-semibold text-primary mb-4">Bons de Livraison</h4>
                        <div x-show="loading" class="text-center py-4">
                            <svg class="animate-spin h-8 w-8 text-primary mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div x-show="!loading && bonsLivraison.length === 0" class="text-center py-4 text-secondary">
                            Aucun bon de livraison disponible pour ce dentiste
                        </div>
                        <div x-show="!loading && bonsLivraison.length > 0" class="space-y-2 max-h-64 overflow-y-auto">
                            <template x-for="bl in bonsLivraison" :key="bl.id">
                                <div class="flex items-center p-3 border border-border rounded-lg hover:bg-neutral-50 transition-colors">
                                    <input 
                                        type="checkbox" 
                                        :value="bl.id"
                                        @change="if ($event.target.checked) { if (!selectedBLs.includes(bl.id)) selectedBLs.push(bl.id); } else { selectedBLs = selectedBLs.filter(id => id !== bl.id); }"
                                        :checked="selectedBLs.includes(bl.id)"
                                        class="rounded border-border text-primary focus:ring-primary mr-3"
                                    >
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-primary" x-text="'BL #' + bl.numero_bl"></div>
                                        <div class="text-xs text-secondary" x-text="'Commande: ' + bl.commande_num + ' | Date: ' + bl.date + ' | Montant: ' + parseFloat(bl.total_ttc).toFixed(2) + ' TND'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <!-- Hidden inputs for selected BLs -->
                        <template x-for="blId in selectedBLs" :key="blId">
                            <input type="hidden" :name="'bon_livraison_ids[]'" :value="blId">
                        </template>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-border">
                        <button 
                            type="button" 
                            @click="showCreateModal = false; selectedDentist = ''; factureStatus = 'pending'; montantPaye = ''; bonsLivraison = []; selectedBLs = []" 
                            class="px-4 py-2 text-sm sm:text-base font-medium text-secondary bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors duration-200"
                        >
                            Annuler
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-sm sm:text-base font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="selectedBLs.length === 0"
                        >
                            Créer la facture
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de facture -->
    <div x-show="showEditModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="closeEditModal()">
        <div x-on:click.stop class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
        <div class="bg-white rounded-2xl max-w-4xl w-full relative z-10 overflow-hidden border border-gray-100 max-h-[90vh] overflow-y-auto" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            <div class="px-4 py-5 sm:px-6 sm:py-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg sm:text-xl font-semibold text-primary">Modifier la facture</h3>
                    <button @click="closeEditModal()" class="text-secondary hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form :action="'{{ url('admin/factures') }}/' + editingFactureId" method="POST" id="editFactureForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="edit_titre_document" class="block text-sm font-medium text-primary mb-2">Titre du document</label>
                            <select 
                                name="titre_document" 
                                id="edit_titre_document" 
                                x-model="titreDocument"
                                class="block w-full input-field"
                                required
                            >
                                @foreach(\App\Models\Facture::getTitreDocumentOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="edit_dentist_id" class="block text-sm font-medium text-primary mb-2">Dentiste</label>
                            <select 
                                name="dentist_id" 
                                id="edit_dentist_id" 
                                x-model="selectedDentist"
                                @change="loadBonsLivraison()"
                                class="block w-full input-field"
                                required
                            >
                                <option value="">Sélectionner un dentiste</option>
                                @foreach($dentists as $dentist)
                                    <option value="{{ $dentist->id }}">{{ $dentist->full_name ?: $dentist->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_date" class="block text-sm font-medium text-primary mb-2">Date de facturation</label>
                                <input 
                                    type="date" 
                                    name="date" 
                                    id="edit_date" 
                                    x-model="factureDate"
                                    class="block w-full input-field"
                                    required
                                >
                            </div>

                            <div>
                                <label for="edit_num_facture" class="block text-sm font-medium text-primary mb-2">Numéro de facture</label>
                                <input 
                                    type="text" 
                                    name="num_facture" 
                                    id="edit_num_facture" 
                                    x-model="numFacture"
                                    class="block w-full input-field"
                                    required
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_ancien_solde" class="block text-sm font-medium text-primary mb-2">Ancien Solde</label>
                                <input 
                                    type="number" 
                                    step="0.01"
                                    name="ancien_solde" 
                                    id="edit_ancien_solde" 
                                    x-model="ancienSolde"
                                    class="block w-full input-field"
                                    placeholder="0.00"
                                >
                            </div>
                            <div>
                                <label for="edit_avance" class="block text-sm font-medium text-primary mb-2">Avance</label>
                                <input 
                                    type="number" 
                                    step="0.01"
                                    name="avance" 
                                    id="edit_avance" 
                                    x-model="avance"
                                    class="block w-full input-field"
                                    placeholder="0.00"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="edit_status" class="block text-sm font-medium text-primary mb-2">Statut</label>
                            <select 
                                name="status" 
                                id="edit_status" 
                                x-model="factureStatus"
                                class="block w-full input-field"
                                required
                            >
                                @foreach(\App\Models\Facture::getStatuses() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Liste des BL -->
                    <div x-show="selectedDentist" style="display: none;">
                        <h4 class="text-md font-semibold text-primary mb-4">Bons de Livraison</h4>
                        <div x-show="loading" class="text-center py-4">
                            <svg class="animate-spin h-8 w-8 text-primary mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div x-show="!loading && bonsLivraison.length === 0" class="text-center py-4 text-secondary">
                            Aucun bon de livraison disponible pour ce dentiste
                        </div>
                        <div x-show="!loading && bonsLivraison.length > 0" class="space-y-2 max-h-64 overflow-y-auto">
                            <template x-for="bl in bonsLivraison" :key="bl.id">
                                <div class="flex items-center p-3 border border-border rounded-lg hover:bg-neutral-50 transition-colors">
                                    <input 
                                        type="checkbox" 
                                        :value="bl.id"
                                        @change="if ($event.target.checked) { if (!selectedBLs.includes(bl.id)) selectedBLs.push(bl.id); } else { selectedBLs = selectedBLs.filter(id => id !== bl.id); }"
                                        :checked="selectedBLs.includes(bl.id)"
                                        class="rounded border-border text-primary focus:ring-primary mr-3"
                                    >
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-primary" x-text="'BL #' + bl.numero_bl"></div>
                                        <div class="text-xs text-secondary" x-text="'Commande: ' + bl.commande_num + ' | Date: ' + bl.date + ' | Montant: ' + parseFloat(bl.total_ttc).toFixed(2) + ' TND'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <!-- Hidden inputs for selected BLs -->
                        <template x-for="blId in selectedBLs" :key="blId">
                            <input type="hidden" :name="'bon_livraison_ids[]'" :value="blId">
                        </template>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-border">
                        <button 
                            type="button" 
                            @click="closeEditModal()" 
                            class="px-4 py-2 text-sm sm:text-base font-medium text-secondary bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors duration-200"
                        >
                            Annuler
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-sm sm:text-base font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="selectedBLs.length === 0"
                        >
                            Enregistrer les modifications
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
                    Êtes-vous sûr de vouloir supprimer la facture <strong x-text="factureToDelete"></strong> ? Cette action est irréversible.
                </p>
                <form :action="deleteFormAction" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <div class="border-t border-border pt-4 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showDeleteModal = false; factureToDelete = null; deleteFormAction = ''" 
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
</x-app-layout>
</div>

