<div x-data="{ showDeleteModal: false, userToDelete: null, deleteFormAction: '' }" x-cloak>
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Gestion des Dentistes') }}
                </h2>
            </div>
            @can('create_users')
            <a href="{{ route('admin.dentists.create') }}" class="btn-primary inline-flex items-center w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden sm:inline">Nouveau Dentiste</span>
                <span class="sm:hidden">Nouveau</span>
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

                <form method="GET" action="{{ route('admin.dentists.index') }}" class="mb-4 sm:mb-6">
                    <div class="flex flex-nowrap items-end gap-3 overflow-x-auto">
                        <div class="flex-1 min-w-[180px]">
                            <label for="filter-search" class="block text-sm font-medium text-primary mb-2">Recherche</label>
                            <input
                                id="filter-search"
                                type="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Nom, prénom, n°, tél, ville…"
                                class="block w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm input-field h-10 sm:h-11"
                            />
                        </div>
                        <div class="w-[200px] shrink-0">
                            <label for="filter-approval" class="block text-sm font-medium text-primary mb-2">Statut</label>
                            <select
                                id="filter-approval"
                                name="approval"
                                class="block w-full input-field h-10 sm:h-11"
                            >
                                <option value="">Tous les statuts</option>
                                <option value="approved" {{ request('approval') === 'approved' ? 'selected' : '' }}>Approuvé</option>
                                <option value="pending" {{ request('approval') === 'pending' ? 'selected' : '' }}>Non approuvé</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary h-10 sm:h-11 px-4 shrink-0 whitespace-nowrap">
                            Filtrer
                        </button>
                        @if(request()->filled('search') || request()->filled('approval'))
                            <a href="{{ route('admin.dentists.index') }}" class="inline-flex items-center justify-center h-10 sm:h-11 px-4 shrink-0 whitespace-nowrap rounded-lg border border-border bg-card text-secondary hover:bg-neutral-100 font-medium text-sm transition-colors duration-200">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-visible">
                    <table class="w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Nom</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Statut</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Numéro</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden md:table-cell">Téléphone</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden lg:table-cell">Gouvernorat</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden lg:table-cell">Ville</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Commandes</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Factures</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-card divide-y divide-border">
                            @forelse($users as $user)
                                <tr class="hover:bg-neutral-100/50 transition-colors">
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold text-xs sm:text-sm mr-2 sm:mr-3">
                                                {{ strtoupper(substr($user->full_name ?: $user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-primary">{{ $user->full_name ?: $user->name }}</div>
                                                <div class="text-xs text-secondary sm:hidden mt-1">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        @if($user->approved_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-secondary/10 text-accent-secondary">
                                                Approuvé
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning/10 text-warning">
                                                En attente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-primary font-medium">{{ $user->num_dentist ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <div class="text-sm text-secondary">{{ $user->tél ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                        <div class="text-sm text-secondary">{{ $user->gouvernorat ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                        <div class="text-sm text-secondary">{{ $user->ville ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.dentists.commandes.index', $user) }}" class="text-blue-600 hover:text-blue-700 font-medium">Voir Commandes</a>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.dentists.factures.index', $user) }}" class="text-blue-600 hover:text-blue-700 font-medium">Voir Factures</a>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.dentists.show', $user) }}" class="text-primary hover:text-primary/80 transition-colors" title="Voir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @can('edit_users')
                                                @if(!$user->approved_at)
                                                    <form method="POST" action="{{ route('admin.dentists.approve', $user) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-accent-secondary hover:text-accent-secondary/80 transition-colors" title="Approuver">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.dentists.revoke', $user) }}" class="inline" onsubmit="return confirm('Révoquer l\'accès de ce dentiste ?');">
                                                        @csrf
                                                        <button type="submit" class="text-warning hover:text-warning/80 transition-colors" title="Révoquer l'accès">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            <a href="{{ route('admin.dentists.edit', $user) }}" class="text-warning hover:text-warning/80 transition-colors" title="Modifier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            @endcan
                                            @can('delete_users')
                                            <button 
                                                type="button" 
                                                @click="showDeleteModal = true; userToDelete = '{{ $user->full_name ?? $user->name }}'; deleteFormAction = '{{ route('admin.users.destroy', $user) }}'"
                                                class="text-danger hover:text-danger/80 transition-colors" 
                                                title="Supprimer"
                                            >
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
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                            <p class="text-secondary text-base font-medium">Aucun dentiste trouvé</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 px-4 sm:px-6 py-4 border-t border-border">
                    <p class="text-sm text-secondary">Affichage de {{ $users->firstItem() }} à {{ $users->lastItem() }} sur {{ $users->total() }} résultats</p>
                    {{ $users->onEachSide(2)->links() }}
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
                    Êtes-vous sûr de vouloir supprimer le dentiste <strong x-text="userToDelete"></strong> ? Cette action est irréversible.
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
