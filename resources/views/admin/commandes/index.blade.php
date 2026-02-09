<div x-data="{ 
    showDeleteModal: false, 
    commandeToDelete: null, 
    deleteFormAction: '', 
    searchQuery: '',
    matchesSearch(rowSearchText) {
        if (!this.searchQuery) return true;
        return rowSearchText.toLowerCase().includes(this.searchQuery.toLowerCase());
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
                    {{ __('Liste des Commandes') }}
                </h2>
            </div>
            @can('create_commandes')
            <a href="{{ route('admin.commandes.create') }}" class="btn-primary inline-flex items-center w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden sm:inline">Nouvelle Commande</span>
                <span class="sm:hidden">Nouveau</span>
            </a>
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

                <!-- Barre de filtre -->
                <div class="mb-4 sm:mb-6">
                    <div class="w-1/2">
                        <input 
                            type="text" 
                            x-model="searchQuery"
                            placeholder="Filter par dentiste ou par patient"
                            class="block w-full px-3 py-2 sm:py-3 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base input-field"
                        />
                    </div>
                </div>

                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Numéro</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Patient</th>
                                @unless(auth()->user()->hasRole('dentist'))
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden md:table-cell">Dentiste</th>
                                @endunless
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Statut</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden lg:table-cell">Urgent</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden lg:table-cell">Créé par</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden md:table-cell">Date</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-card divide-y divide-border">
                            @forelse($commandes as $commande)
                                @php
                                    $dentistName = $commande->dentiste->full_name ?? $commande->dentiste->name ?? '';
                                    $patientName = $commande->nom_patient ?? '';
                                    $searchText = strtolower($dentistName . ' ' . $patientName);
                                @endphp
                                <tr 
                                    class="hover:bg-neutral-100/50 transition-colors"
                                    x-show="matchesSearch('{{ $searchText }}')"
                                    data-search="{{ $searchText }}"
                                >
                                    <td class="px-3 sm:px-6 py-4">
                                        <span class="text-sm font-semibold text-primary">{{ $commande->num_cmd }}</span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm font-medium text-primary">{{ $commande->nom_patient }}</div>
                                    </td>
                                    @unless(auth()->user()->hasRole('dentist'))
                                    <td class="px-3 sm:px-6 py-4 hidden md:table-cell">
                                        <div class="text-sm text-secondary">{{ $commande->dentiste->full_name ?? $commande->dentiste->name }}</div>
                                    </td>
                                    @endunless
                                    <td class="px-3 sm:px-6 py-4">
                                        @php
                                            $statusConfig = [
                                                'Reçue' => ['bg' => 'bg-neutral-100', 'text' => 'text-secondary', 'border' => 'border-border'],
                                                'En cours' => ['bg' => 'bg-warning/10', 'text' => 'text-warning', 'border' => 'border-warning/30'],
                                                'Terminée' => ['bg' => 'bg-accent-secondary/10', 'text' => 'text-accent-secondary', 'border' => 'border-accent-secondary/30'],
                                                'Livrée' => ['bg' => 'bg-primary/10', 'text' => 'text-primary', 'border' => 'border-primary/30'],
                                            ];
                                            $config = $statusConfig[$commande->status] ?? $statusConfig['Reçue'];
                                        @endphp
                                        <div class="flex flex-col gap-1">
                                            <span class="px-2 sm:px-3 py-1 inline-flex text-xs font-medium rounded-md {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['border'] }}">
                                                {{ $commande->status }}
                                            </span>
                                            @if($commande->status === 'Terminée' && $commande->finishedBy)
                                                <span class="text-xs text-secondary mt-1">
                                                    Par: {{ $commande->finishedBy->full_name ?? $commande->finishedBy->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 hidden lg:table-cell">
                                        @if($commande->urgent)
                                            <span class="inline-flex items-center px-2 sm:px-3 py-1 text-xs font-medium rounded-md bg-danger/10 text-danger border border-danger/30">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                Urgent
                                            </span>
                                        @else
                                            <span class="text-secondary">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 hidden lg:table-cell">
                                        <div class="text-sm text-secondary">
                                            @if($commande->createdBy)
                                                {{ $commande->createdBy->full_name ?? $commande->createdBy->name }}
                                            @else
                                                <span class="text-secondary">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 hidden md:table-cell">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-secondary">{{ $commande->created_at->format('d/m/Y') }}</span>
                                            <span class="text-xs text-secondary/70 mt-0.5">{{ $commande->created_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.commandes.show', $commande) }}" class="text-primary hover:text-primary/80 transition-colors" title="Voir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @can('edit_commandes')
                                                @unless(auth()->user()->hasRole('dentist') && $commande->status === 'Livrée')
                                                <a href="{{ route('admin.commandes.edit', $commande) }}" class="text-warning hover:text-warning/80 transition-colors" title="Modifier">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                @endunless
                                            @endcan
                                            @if(auth()->user()->hasRole('dentist') && $commande->status === 'Reçue')
                                                @can('delete_commandes')
                                                <button 
                                                    type="button" 
                                                    @click="showDeleteModal = true; commandeToDelete = '{{ $commande->num_cmd }}'; deleteFormAction = '{{ route('admin.commandes.destroy', $commande) }}'"
                                                    class="text-danger hover:text-danger/80 transition-colors" 
                                                    title="Supprimer"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                                @endcan
                                            @elseif(!auth()->user()->hasRole('dentist'))
                                                @can('delete_commandes')
                                                <button 
                                                    type="button" 
                                                    @click="showDeleteModal = true; commandeToDelete = '{{ $commande->num_cmd }}'; deleteFormAction = '{{ route('admin.commandes.destroy', $commande) }}'"
                                                    class="text-danger hover:text-danger/80 transition-colors" 
                                                    title="Supprimer"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->hasRole('dentist') ? '6' : '8' }}" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-secondary text-base font-medium">Aucune commande trouvée</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            @if(count($commandes) > 0)
                                <tr 
                                    x-show="searchQuery && Array.from(document.querySelectorAll('tbody tr[data-search]')).every(tr => {
                                        const searchText = tr.getAttribute('data-search') || '';
                                        return !matchesSearch(searchText);
                                    })"
                                    style="display: none;"
                                >
                                    <td colspan="{{ auth()->user()->hasRole('dentist') ? '6' : '8' }}" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <p class="text-secondary text-base font-medium">Aucun résultat pour "<span x-text="searchQuery"></span>"</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if($commandes->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 px-4 sm:px-6 py-4 border-t border-border">
                    <p class="text-sm text-secondary">Affichage de {{ $commandes->firstItem() }} à {{ $commandes->lastItem() }} sur {{ $commandes->total() }} résultats</p>
                    {{ $commandes->onEachSide(2)->links() }}
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
</x-app-layout>
</div>