<div x-data="{
    showEditModal: false,
    showDeleteModal: false,
    selectedDentist: '{{ addslashes($facture->dentist_id) }}',
    factureDate: '{{ $facture->date->format('Y-m-d') }}',
    numFacture: '{{ addslashes($facture->num_facture) }}',
    factureStatus: '{{ addslashes($facture->status) }}',
    montantPaye: {{ $facture->montant_paye ?? 0 }},
    montantRestant: {{ $facture->montant_restant ?? 0 }},
    bonsLivraison: [],
    selectedBLs: [{{ $facture->bonsLivraison->pluck('id')->implode(',') ?: '' }}],
    loading: false,
    async loadBonsLivraison() {
        if (!this.selectedDentist) {
            this.bonsLivraison = [];
            return;
        }
        this.loading = true;
        try {
            const response = await fetch(`{{ route('admin.factures.get-bons-livraison') }}?dentist_id=${this.selectedDentist}&facture_id={{ $facture->id }}`);
            const data = await response.json();
            if (data.success) {
                this.bonsLivraison = data.bonsLivraison;
                this.bonsLivraison.forEach(bl => {
                    if (bl.is_selected && !this.selectedBLs.includes(bl.id)) {
                        this.selectedBLs.push(bl.id);
                    }
                });
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            this.loading = false;
        }
    },
    openEditModal() {
        this.showEditModal = true;
        this.loadBonsLivraison();
    },
    closeEditModal() {
        this.showEditModal = false;
    },
    openDeleteModal() {
        this.showDeleteModal = true;
    },
}">
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
                    Facture #{{ $facture->num_facture }}
                </h2>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.factures.index') }}" class="btn-secondary inline-flex items-center justify-center w-full sm:w-auto">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour
                </a>
                @can('view_factures')
                <a href="{{ route('admin.factures.print', $facture) }}" target="_blank" class="btn-primary inline-flex items-center justify-center w-full sm:w-auto" style="background-color: #3B82F6; border-color: #3B82F6;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimer
                </a>
                @endcan
                @can('edit_factures')
                <button @click="openEditModal()" class="btn-primary inline-flex items-center justify-center w-full sm:w-auto" style="background-color: #F59E0B; border-color: #F59E0B;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </button>
                @endcan
                @can('delete_factures')
                <button @click="openDeleteModal()" class="btn-primary inline-flex items-center justify-center w-full sm:w-auto" style="background-color: #EF4444; border-color: #EF4444;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Supprimer
                </button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Informations Générales -->
            <div class="card mb-6">
                <h3 class="text-lg font-semibold text-primary mb-4">Informations Générales</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-secondary mb-1">Numéro de facture</p>
                        <p class="font-medium text-primary">{{ $facture->num_facture }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-secondary mb-1">Date</p>
                        <p class="font-medium text-primary">{{ $facture->date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-secondary mb-1">Dentiste</p>
                        <p class="font-medium text-primary">{{ $facture->dentist->full_name ?? $facture->dentist->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-secondary mb-1">Montant Total</p>
                        <p class="font-medium text-primary text-lg">{{ number_format($facture->montant, 2, ',', ' ') }} TND</p>
                    </div>
                    <div>
                        <p class="text-sm text-secondary mb-1">Statut</p>
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
                    </div>
                    @if($facture->status === 'partially_paid')
                    <div>
                        <p class="text-sm text-secondary mb-1">Montant payé</p>
                        <p class="font-medium text-primary" x-text="new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(montantPaye) + ' TND'">{{ number_format($facture->montant_paye ?? 0, 2, ',', ' ') }} TND</p>
                    </div>
                    <div>
                        <p class="text-sm text-secondary mb-1">Montant restant</p>
                        <p class="font-medium text-primary" x-text="new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(montantRestant) + ' TND'">{{ number_format($facture->montant_restant ?? 0, 2, ',', ' ') }} TND</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Bons de Livraison -->
            <div class="card">
                <h3 class="text-lg font-semibold text-primary mb-4">Bons de Livraison</h3>
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Numéro BL</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Commande</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Date</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Montant TTC</th>
                            </tr>
                        </thead>
                        <tbody class="bg-card divide-y divide-border">
                            @forelse($facture->bonsLivraison as $bl)
                                <tr class="hover:bg-neutral-100/50 transition-colors">
                                    <td class="px-3 sm:px-6 py-4">
                                        <span class="text-sm font-semibold text-primary">{{ $bl->numero_bl }}</span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm text-secondary">{{ $bl->commande->num_cmd ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm text-secondary">{{ $bl->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm font-medium text-primary">{{ number_format($bl->total_ttc, 2, ',', ' ') }} TND</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-secondary text-base font-medium">Aucun bon de livraison</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Échéances de Paiement -->
            @if($facture->status === 'partially_paid')
            <div class="card mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary">Échéances de Paiement</h3>
                    @can('edit_factures')
                    <button 
                        type="button"
                        onclick="openEcheanceModal()"
                        class="btn-primary inline-flex items-center text-sm"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Ajouter une échéance
                    </button>
                    @endcan
                </div>

                <!-- Liste des échéances -->
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Montant</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Mode de règlement</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Date</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Statut de paiement</th>
                                @can('edit_factures')
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="bg-card divide-y divide-border">
                            @forelse($facture->echeances as $echeance)
                                <tr class="hover:bg-neutral-100/50 transition-colors">
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm font-medium text-primary">{{ number_format($echeance->montant, 2, ',', ' ') }} TND</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm text-secondary">{{ $echeance->mode_reglement_formatted }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm text-secondary">{{ $echeance->date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($echeance->statut_paiement === 'Payé') bg-green-100 text-green-800
                                            @elseif($echeance->statut_paiement === 'A encaisser') bg-yellow-100 text-yellow-800
                                            @elseif($echeance->statut_paiement === 'Encaissé') bg-blue-100 text-blue-800
                                            @elseif($echeance->statut_paiement === 'A recevoir') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $echeance->statut_paiement }}
                                        </span>
                                    </td>
                                    @can('edit_factures')
                                    <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button 
                                                type="button"
                                                onclick="openEditEcheanceModal({{ $echeance->id }}, {{ $echeance->montant }}, '{{ addslashes($echeance->mode_reglement_formatted) }}', '{{ $echeance->date->format('Y-m-d') }}', '{{ addslashes($echeance->statut_paiement) }}')"
                                                class="text-warning hover:text-warning/80 transition-colors" 
                                                title="Modifier"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button 
                                                type="button"
                                                onclick="openDeleteEcheanceModal({{ $echeance->id }})" 
                                                class="text-danger hover:text-danger/80 transition-colors" 
                                                title="Supprimer"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->can('edit_factures') ? '5' : '4' }}" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-secondary text-base font-medium">Aucune échéance de paiement</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal d'ajout/édition d'échéance -->
    <div id="echeanceModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm" onclick="closeEcheanceModal()"></div>
        <div class="bg-white rounded-2xl max-w-4xl w-full relative z-10 overflow-hidden border border-gray-100 max-h-[90vh] overflow-y-auto" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);" onclick="event.stopPropagation()">
            <div class="px-4 py-5 sm:px-6 sm:py-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg sm:text-xl font-semibold text-primary" id="echeanceModalTitle">Ajouter une échéance</h3>
                    <button onclick="closeEcheanceModal()" class="text-secondary hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form 
                    onsubmit="submitEcheanceForm(event)"
                    id="echeanceForm"
                >
                    @csrf
                    <input type="hidden" name="_method" id="echeanceMethod" value="POST">
                    <input type="hidden" id="editingEcheanceId" value="">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="montant" class="block text-sm font-medium text-primary mb-2">Montant</label>
                            <input 
                                type="number" 
                                step="0.01"
                                name="montant" 
                                id="montant" 
                                class="block w-full input-field"
                                required
                            >
                        </div>

                        <div>
                            <label for="mode_reglement" class="block text-sm font-medium text-primary mb-2">Mode de règlement</label>
                            <select 
                                name="mode_reglement" 
                                id="mode_reglement" 
                                onchange="updateStatutPaiement(this.value)"
                                class="block w-full input-field"
                                required
                            >
                                <option value="">Sélectionner un mode</option>
                                <option value="Espèces">Espèces</option>
                                <option value="Virement bancaire">Virement bancaire</option>
                                <option value="Chèque">Chèque</option>
                                <option value="Lettre de change (كمبيالة)">Lettre de change (كمبيالة)</option>
                            </select>
                        </div>

                        <div>
                            <label for="echeance-date-input" class="block text-sm font-medium text-primary mb-2">Date</label>
                            <input 
                                id="echeance-date-input" 
                                name="date" 
                                type="text" 
                                class="block w-full input-field" 
                                placeholder="Sélectionner une date"
                                required
                            />
                        </div>

                        <div>
                            <label for="statut_paiement" class="block text-sm font-medium text-primary mb-2">Statut de paiement</label>
                            <select 
                                name="statut_paiement" 
                                id="statut_paiement" 
                                class="block w-full input-field"
                                required
                            >
                                <option value="">Sélectionner un statut</option>
                                <option value="Payé">Payé</option>
                                <option value="A encaisser">A encaisser</option>
                                <option value="Encaissé">Encaissé</option>
                                <option value="A recevoir">A recevoir</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-border">
                        <button 
                            type="button" 
                            onclick="closeEcheanceModal()" 
                            class="px-4 py-2 text-sm sm:text-base font-medium text-secondary bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors duration-200"
                        >
                            Annuler
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-sm sm:text-base font-medium text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        >
                            <span id="echeanceSubmitText">Ajouter l'échéance</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de suppression d'échéance -->
    <div id="deleteEcheanceModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0" style="display: none;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm" onclick="closeDeleteEcheanceModal()"></div>
        <div class="bg-white rounded-2xl max-w-sm sm:max-w-md w-full relative z-10 overflow-hidden border border-gray-100" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);" onclick="event.stopPropagation()">
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
                    Êtes-vous sûr de vouloir supprimer cette échéance ? Cette action est irréversible.
                </p>
                <div class="border-t border-border pt-4 flex justify-end space-x-3">
                    <button 
                        type="button" 
                        onclick="closeDeleteEcheanceModal()" 
                        class="px-4 py-2 text-sm sm:text-base font-medium text-secondary bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors duration-200"
                    >
                        Annuler
                    </button>
                    <button 
                        type="button"
                        onclick="deleteEcheance()" 
                        class="px-4 py-2 text-sm sm:text-base font-medium text-white bg-danger hover:bg-red-700 rounded-lg transition-colors duration-200 shadow-sm"
                    >
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de facture -->
    <div x-show="showEditModal" 
         x-cloak
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
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

                <form action="{{ route('admin.factures.update', $facture) }}" method="POST" id="editFactureForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4 mb-6">
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
                                @foreach(\App\Models\User::role('dentist')->orderBy('order')->get() as $dentist)
                                    <option value="{{ $dentist->id }}" {{ $facture->dentist_id == $dentist->id ? 'selected' : '' }}>{{ $dentist->full_name ?: $dentist->name }}</option>
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
                                    <option value="{{ $value }}" :selected="factureStatus === '{{ $value }}'">{{ $label }}</option>
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
         x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
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
                    Êtes-vous sûr de vouloir supprimer la facture <strong>{{ $facture->num_facture }}</strong> ? Cette action est irréversible.
                </p>
                <form action="{{ route('admin.factures.destroy', $facture) }}" method="POST" class="inline">
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

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    <script>
        let echeanceDatePicker = null;
        let editingEcheanceId = null;
        let echeanceToDelete = null;

        function openEcheanceModal() {
            editingEcheanceId = null;
            document.getElementById('echeanceModalTitle').textContent = 'Ajouter une échéance';
            document.getElementById('echeanceSubmitText').textContent = 'Ajouter l\'échéance';
            document.getElementById('echeanceMethod').value = 'POST';
            document.getElementById('editingEcheanceId').value = '';
            document.getElementById('montant').value = '';
            document.getElementById('mode_reglement').value = '';
            document.getElementById('statut_paiement').value = '';
            document.getElementById('echeanceModal').style.display = 'flex';
            
            setTimeout(() => {
                initEcheanceDatePicker('{{ now()->format('Y-m-d') }}');
            }, 100);
        }

        function openEditEcheanceModal(id, montant, modeReglement, date, statutPaiement) {
            editingEcheanceId = id;
            document.getElementById('echeanceModalTitle').textContent = 'Modifier l\'échéance';
            document.getElementById('echeanceSubmitText').textContent = 'Enregistrer les modifications';
            document.getElementById('echeanceMethod').value = 'PUT';
            document.getElementById('editingEcheanceId').value = id;
            document.getElementById('montant').value = montant;
            document.getElementById('mode_reglement').value = modeReglement;
            document.getElementById('statut_paiement').value = statutPaiement;
            document.getElementById('echeanceModal').style.display = 'flex';
            
            setTimeout(() => {
                initEcheanceDatePicker(date);
            }, 100);
        }

        function closeEcheanceModal() {
            document.getElementById('echeanceModal').style.display = 'none';
            if (echeanceDatePicker) {
                echeanceDatePicker.destroy();
                echeanceDatePicker = null;
            }
        }

        function openDeleteEcheanceModal(id) {
            echeanceToDelete = id;
            document.getElementById('deleteEcheanceModal').style.display = 'flex';
        }

        function closeDeleteEcheanceModal() {
            document.getElementById('deleteEcheanceModal').style.display = 'none';
            echeanceToDelete = null;
        }

        function updateStatutPaiement(modeReglement) {
            const statutSelect = document.getElementById('statut_paiement');
            if (modeReglement === 'Espèces' || modeReglement === 'Virement bancaire' || modeReglement === 'Chèque') {
                statutSelect.value = 'Payé';
            } else if (modeReglement === 'Lettre de change (كمبيالة)') {
                statutSelect.value = 'A encaisser';
            }
        }

        function initEcheanceDatePicker(dateValue) {
            const dateInput = document.getElementById('echeance-date-input');
            if (!dateInput) return;
            
            if (echeanceDatePicker) {
                echeanceDatePicker.destroy();
                echeanceDatePicker = null;
            }

            echeanceDatePicker = flatpickr(dateInput, {
                dateFormat: "Y-m-d",
                clickOpens: true,
                allowInput: true,
                locale: "fr",
                defaultDate: dateValue || '{{ now()->format('Y-m-d') }}'
            });
            
            // S'assurer que la valeur est bien définie dans l'input
            if (dateValue) {
                dateInput.value = dateValue;
            }
        }

        async function submitEcheanceForm(event) {
            event.preventDefault();
            const form = event.target;
            
            // Récupérer la date depuis Flatpickr et la mettre dans l'input
            const dateInput = document.getElementById('echeance-date-input');
            if (echeanceDatePicker && echeanceDatePicker.selectedDates.length > 0) {
                const formattedDate = echeanceDatePicker.formatDate(echeanceDatePicker.selectedDates[0], 'Y-m-d');
                dateInput.value = formattedDate;
            }
            
            // Utiliser FormData directement depuis le formulaire pour s'assurer que tous les champs sont inclus
            const formData = new FormData(form);
            
            // Vérifier que tous les champs requis sont remplis
            const montant = formData.get('montant');
            const modeReglement = formData.get('mode_reglement');
            const date = formData.get('date');
            const statutPaiement = formData.get('statut_paiement');
            
            if (!montant || !modeReglement || !date || !statutPaiement) {
                alert('Veuillez remplir tous les champs requis:\n- Montant: ' + (montant || 'vide') + '\n- Mode de règlement: ' + (modeReglement || 'vide') + '\n- Date: ' + (date || 'vide') + '\n- Statut: ' + (statutPaiement || 'vide'));
                return;
            }
            
            // Déterminer l'URL et la méthode
            let url;
            const method = 'POST';
            
            // Récupérer editingEcheanceId depuis le champ caché
            const editingId = document.getElementById('editingEcheanceId').value;
            
            if (editingId) {
                // Mode édition : utiliser PUT via _method
                url = `{{ url('admin/factures/' . $facture->id . '/echeances') }}/${editingId}`;
                formData.set('_method', 'PUT');
            } else {
                // Mode création
                url = '{{ route('admin.factures.echeances.store', $facture) }}';
            }
            
            // Debug: afficher les valeurs envoyées
            console.log('Données envoyées:', {
                montant: formData.get('montant'),
                mode_reglement: formData.get('mode_reglement'),
                date: formData.get('date'),
                statut_paiement: formData.get('statut_paiement'),
                _method: formData.get('_method'),
                editingId: editingId
            });
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    window.location.reload();
                } else {
                    // Afficher les erreurs de validation de manière plus détaillée
                    let errorMessage = 'Erreur: ';
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat();
                        errorMessage += errorMessages.join(', ');
                    } else if (data.message) {
                        errorMessage += data.message;
                    } else {
                        errorMessage += 'Une erreur est survenue';
                    }
                    alert(errorMessage);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Erreur lors de la sauvegarde: ' + error.message);
            }
        }

        async function deleteEcheance() {
            if (!echeanceToDelete) return;

            try {
                const response = await fetch(`{{ url('admin/factures/' . $facture->id . '/echeances') }}/${echeanceToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Erreur lors de la suppression');
            }
        }
    </script>
    @endpush
</x-app-layout>
</div>