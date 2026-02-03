<div x-data="{ 
    showDeleteGroupeModal: false, 
    groupeToDelete: null, 
    deleteGroupeFormAction: '',
    showEditGroupeModal: false,
    showCreateGroupeModal: false,
    editingGroupe: null,
    editGroupeForm: {
        nom: '',
        description: ''
    },
    createGroupeForm: {
        nom: '',
        description: ''
    },
    showDeleteCritereModal: false, 
    critereToDelete: null, 
    deleteCritereFormAction: '',
    showEditCritereModal: false,
    showCreateCritereModal: false,
    editingCritere: null,
    editCritereForm: {
        nom: '',
        groupe_id: '',
        type: ''
    },
    createCritereForm: {
        nom: '',
        groupe_id: '',
        type: ''
    }
}" x-cloak>
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-accent-secondary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Configuration') }}
                </h2>
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

            <!-- Bloc 1: Gestion de groupe -->
            <div class="card mb-6">
                <div class="mb-4 sm:mb-6 flex justify-between items-center">
                    <h3 class="text-base sm:text-lg font-semibold text-primary flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Gestion de groupe
                    </h3>
                    <button 
                        type="button" 
                        @click="showCreateGroupeModal = true; createGroupeForm.nom = ''; createGroupeForm.description = ''"
                        class="btn-primary"
                    >
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Ajouter
                    </button>
                </div>
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Nom</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Description</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-card divide-y divide-border">
                            @forelse($groupes as $groupe)
                                <tr class="hover:bg-neutral-100/50 transition-colors">
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        <span class="text-primary font-medium">{{ $groupe->nom }}</span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <span class="text-secondary">{{ $groupe->description ?? '-' }}</span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-3">
                                            <button 
                                                type="button" 
                                                data-groupe-id="{{ $groupe->id }}"
                                                data-groupe-nom="{{ $groupe->nom }}"
                                                data-groupe-description="{{ $groupe->description ?? '' }}"
                                                @click="showEditGroupeModal = true; editingGroupe = $el.dataset.groupeId; editGroupeForm.nom = $el.dataset.groupeNom; editGroupeForm.description = $el.dataset.groupeDescription || ''"
                                                class="text-warning hover:text-warning/80 transition-colors" 
                                                title="Modifier le groupe"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="showDeleteGroupeModal = true; groupeToDelete = '{{ $groupe->nom }}'; deleteGroupeFormAction = '{{ route('admin.groupes.destroy', $groupe) }}'"
                                                class="text-danger hover:text-danger/80 transition-colors" 
                                                title="Supprimer le groupe"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <p class="text-secondary text-base font-medium">Aucun groupe</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bloc 2: Critère contrôle qualité -->
            <div class="card">
                <div class="mb-4 sm:mb-6 flex justify-between items-center">
                    <h3 class="text-base sm:text-lg font-semibold text-primary flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Critère contrôle qualité
                    </h3>
                    <button 
                        type="button" 
                        @click="showCreateCritereModal = true; createCritereForm.nom = ''; createCritereForm.groupe_id = ''; createCritereForm.type = ''"
                        class="btn-primary"
                    >
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Ajouter
                    </button>
                </div>
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Nom</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Groupe</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Type</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-card divide-y divide-border">
                            @forelse($criteres as $critere)
                                <tr class="hover:bg-neutral-100/50 transition-colors">
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        <span class="text-primary font-medium">{{ $critere->nom }}</span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        <span class="text-secondary">{{ $critere->groupe ? $critere->groupe->nom : '-' }}</span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                        <span class="text-secondary">{{ $critere->type ? $critere->type->value : '-' }}</span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-3">
                                            <button 
                                                type="button" 
                                                data-critere-id="{{ $critere->id }}"
                                                data-critere-nom="{{ $critere->nom }}"
                                                data-critere-groupe="{{ $critere->groupe_id ?? '' }}"
                                                data-critere-type="{{ $critere->type ? $critere->type->value : '' }}"
                                                @click="showEditCritereModal = true; editingCritere = $el.dataset.critereId; editCritereForm.nom = $el.dataset.critereNom; editCritereForm.groupe_id = $el.dataset.critereGroupe || ''; editCritereForm.type = $el.dataset.critereType || ''"
                                                class="text-warning hover:text-warning/80 transition-colors" 
                                                title="Modifier le critère"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="showDeleteCritereModal = true; critereToDelete = '{{ $critere->nom }}'; deleteCritereFormAction = '{{ route('admin.criteres-quality.destroy', $critere) }}'"
                                                class="text-danger hover:text-danger/80 transition-colors" 
                                                title="Supprimer le critère"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-secondary text-base font-medium">Aucun critère de qualité</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de groupe -->
    <div x-show="showEditGroupeModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="showEditGroupeModal = false">
        <div x-on:click.stop class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
        <div class="bg-white rounded-2xl w-full sm:max-w-md relative z-10 overflow-hidden border border-gray-100 mx-auto" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            <div class="px-4 py-5 sm:px-6 sm:py-6">
                <div class="flex items-center mb-4 gap-3 sm:gap-3">
                    <div class="flex-shrink-0 bg-primary/10 rounded-full p-2">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-primary">Modifier le groupe</h3>
                </div>
                <form x-bind:action="'{{ url('admin/groupes') }}/' + editingGroupe" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <x-label for="edit_groupe_nom" value="{{ __('Nom') }}" class="text-primary font-medium mb-2" />
                        <x-input 
                            id="edit_groupe_nom" 
                            name="nom" 
                            type="text" 
                            class="input-field" 
                            x-model="editGroupeForm.nom" 
                            required
                        />
                        <x-input-error for="nom" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="edit_groupe_description" value="{{ __('Description') }}" class="text-primary font-medium mb-2" />
                        <textarea 
                            id="edit_groupe_description" 
                            name="description" 
                            class="input-field" 
                            x-model="editGroupeForm.description"
                            rows="3"
                        ></textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>
                    <div class="border-t border-border pt-4 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showEditGroupeModal = false" 
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

    <!-- Modal de création de groupe -->
    <div x-show="showCreateGroupeModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="showCreateGroupeModal = false">
        <div x-on:click.stop class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
        <div class="bg-white rounded-2xl w-full sm:max-w-md relative z-10 overflow-hidden border border-gray-100 mx-auto" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            <div class="px-4 py-5 sm:px-6 sm:py-6">
                <div class="flex items-center mb-4 gap-3 sm:gap-3">
                    <div class="flex-shrink-0 bg-primary/10 rounded-full p-2">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-primary">Ajouter un groupe</h3>
                </div>
                <form action="{{ route('admin.groupes.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <x-label for="create_groupe_nom" value="{{ __('Nom') }}" class="text-primary font-medium mb-2" />
                        <x-input 
                            id="create_groupe_nom" 
                            name="nom" 
                            type="text" 
                            class="input-field" 
                            x-model="createGroupeForm.nom" 
                            required
                        />
                        <x-input-error for="nom" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="create_groupe_description" value="{{ __('Description') }}" class="text-primary font-medium mb-2" />
                        <textarea 
                            id="create_groupe_description" 
                            name="description" 
                            class="input-field" 
                            x-model="createGroupeForm.description"
                            rows="3"
                        ></textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>
                    <div class="border-t border-border pt-4 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showCreateGroupeModal = false" 
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

    <!-- Modal de confirmation de suppression de groupe -->
    <div x-show="showDeleteGroupeModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="showDeleteGroupeModal = false">
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
                    Êtes-vous sûr de vouloir supprimer le groupe <strong x-text="groupeToDelete"></strong> ? Cette action est irréversible.
                </p>
                <form :action="deleteGroupeFormAction" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <div class="border-t border-border pt-4 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showDeleteGroupeModal = false" 
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

    <!-- Modal de création de critère -->
    <div x-show="showCreateCritereModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="showCreateCritereModal = false">
        <div x-on:click.stop class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
        <div class="bg-white rounded-2xl w-full sm:max-w-md relative z-10 overflow-hidden border border-gray-100 mx-auto" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            <div class="px-4 py-5 sm:px-6 sm:py-6">
                <div class="flex items-center mb-4 gap-3 sm:gap-3">
                    <div class="flex-shrink-0 bg-primary/10 rounded-full p-2">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-primary">Ajouter un critère de qualité</h3>
                </div>
                <form action="{{ route('admin.criteres-quality.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <x-label for="create_critere_nom" value="{{ __('Nom') }}" class="text-primary font-medium mb-2" />
                        <x-input 
                            id="create_critere_nom" 
                            name="nom" 
                            type="text" 
                            class="input-field" 
                            x-model="createCritereForm.nom" 
                            required
                        />
                        <x-input-error for="nom" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="create_critere_groupe_id" value="{{ __('Groupe') }}" class="text-primary font-medium mb-2" />
                        <select 
                            id="create_critere_groupe_id" 
                            name="groupe_id" 
                            class="input-field" 
                            x-model="createCritereForm.groupe_id"
                            required
                        >
                            <option value="">Sélectionner un groupe</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}">{{ $groupe->nom }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="groupe_id" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="create_critere_type" value="{{ __('Type') }}" class="text-primary font-medium mb-2" />
                        <select 
                            id="create_critere_type" 
                            name="type" 
                            class="input-field" 
                            x-model="createCritereForm.type"
                            required
                        >
                            <option value="">Sélectionner un type</option>
                            <option value="Empreinte">Empreinte</option>
                            <option value="Contrôle visuel">Contrôle visuel</option>
                            <option value="Occlusion">Occlusion</option>
                            <option value="Livraison">Livraison</option>
                            <option value="Marque des Matériaux">Marque des Matériaux</option>
                        </select>
                        <x-input-error for="type" class="mt-2" />
                    </div>
                    <div class="border-t border-border pt-4 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showCreateCritereModal = false" 
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

    <!-- Modal d'édition de critère -->
    <div x-show="showEditCritereModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="showEditCritereModal = false">
        <div x-on:click.stop class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
        <div class="bg-white rounded-2xl w-full sm:max-w-md relative z-10 overflow-hidden border border-gray-100 mx-auto" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            <div class="px-4 py-5 sm:px-6 sm:py-6">
                <div class="flex items-center mb-4 gap-3 sm:gap-3">
                    <div class="flex-shrink-0 bg-primary/10 rounded-full p-2">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-primary">Modifier le critère de qualité</h3>
                </div>
                <form x-bind:action="'{{ url('admin/criteres-quality') }}/' + editingCritere" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <x-label for="edit_critere_nom" value="{{ __('Nom') }}" class="text-primary font-medium mb-2" />
                        <x-input 
                            id="edit_critere_nom" 
                            name="nom" 
                            type="text" 
                            class="input-field" 
                            x-model="editCritereForm.nom" 
                            required
                        />
                        <x-input-error for="nom" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="edit_critere_groupe_id" value="{{ __('Groupe') }}" class="text-primary font-medium mb-2" />
                        <select 
                            id="edit_critere_groupe_id" 
                            name="groupe_id" 
                            class="input-field" 
                            x-model="editCritereForm.groupe_id"
                            required
                        >
                            <option value="">Sélectionner un groupe</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}">{{ $groupe->nom }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="groupe_id" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="edit_critere_type" value="{{ __('Type') }}" class="text-primary font-medium mb-2" />
                        <select 
                            id="edit_critere_type" 
                            name="type" 
                            class="input-field" 
                            x-model="editCritereForm.type"
                            required
                        >
                            <option value="">Sélectionner un type</option>
                            <option value="Empreinte">Empreinte</option>
                            <option value="Contrôle visuel">Contrôle visuel</option>
                            <option value="Occlusion">Occlusion</option>
                            <option value="Livraison">Livraison</option>
                            <option value="Marque des Matériaux">Marque des Matériaux</option>
                        </select>
                        <x-input-error for="type" class="mt-2" />
                    </div>
                    <div class="border-t border-border pt-4 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showEditCritereModal = false" 
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

    <!-- Modal de confirmation de suppression de critère -->
    <div x-show="showDeleteCritereModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-90" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;"
         @click.away="showDeleteCritereModal = false">
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
                    Êtes-vous sûr de vouloir supprimer le critère <strong x-text="critereToDelete"></strong> ? Cette action est irréversible.
                </p>
                <form :action="deleteCritereFormAction" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <div class="border-t border-border pt-4 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showDeleteCritereModal = false" 
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