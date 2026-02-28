<div x-data="{ 
    selectedDentist: '{{ $facture->dentist_id }}',
    factureDate: '{{ $facture->date->format('Y-m-d') }}',
    numFacture: '{{ $facture->num_facture }}',
    bonsLivraison: [],
    selectedBLs: {{ json_encode($facture->bonsLivraison->pluck('id')->toArray()) }},
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
                // Pré-sélectionner les BL qui étaient déjà dans la facture
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
    init() {
        this.loadBonsLivraison();
    }
}" x-cloak>
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    Modifier la facture #{{ $facture->num_facture }}
                </h2>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.factures.show', $facture) }}" class="btn-secondary inline-flex items-center justify-center w-full sm:w-auto">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

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

            <div class="card">
                <form action="{{ route('admin.factures.update', $facture) }}" method="POST" id="factureForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="titre_document" class="block text-sm font-medium text-primary mb-2">Titre du document</label>
                            <select 
                                name="titre_document" 
                                id="titre_document" 
                                class="block w-full input-field"
                                required
                            >
                                @foreach(\App\Models\Facture::getTitreDocumentOptions() as $value => $label)
                                    <option value="{{ $value }}" {{ ($facture->titre_document ?? 'bon_livraison') == $value ? 'selected' : '' }}>{{ $label }}</option>
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
                                    <option value="{{ $dentist->id }}" {{ $facture->dentist_id == $dentist->id ? 'selected' : '' }}>{{ $dentist->full_name ?: $dentist->name }}</option>
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
                                    value="{{ $facture->ancien_solde ?? 0 }}"
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
                                    value="{{ $facture->avance ?? 0 }}"
                                    class="block w-full input-field"
                                    placeholder="0.00"
                                >
                            </div>
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
                        <a 
                            href="{{ route('admin.factures.show', $facture) }}"
                            class="px-4 py-2 text-sm sm:text-base font-medium text-secondary bg-neutral-100 hover:bg-neutral-200 rounded-lg transition-colors duration-200"
                        >
                            Annuler
                        </a>
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
</x-app-layout>
</div>
