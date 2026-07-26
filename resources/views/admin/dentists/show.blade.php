<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Dentiste : ') . $user->full_name }}
                </h2>
            </div>
            <div class="flex flex-row gap-2">
                @can('edit_users')
                    @if(!$user->approved_at)
                        <form method="POST" action="{{ route('admin.dentists.approve', $user) }}">
                            @csrf
                            <button type="submit" class="btn-primary inline-flex items-center justify-center" style="background-color: #10B981; border-color: #10B981;">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Approuver
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.dentists.revoke', $user) }}" onsubmit="return confirm('Révoquer l\'accès de ce dentiste ?');">
                            @csrf
                            <button type="submit" class="btn-primary inline-flex items-center justify-center" style="background-color: #F59E0B; border-color: #F59E0B;">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                                Révoquer
                            </button>
                        </form>
                    @endif
                <a href="{{ route('admin.dentists.edit', $user) }}" class="btn-primary inline-flex items-center justify-center" style="background-color: #F59E0B; border-color: #F59E0B;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
                @endcan
                <a href="{{ route('admin.dentists.index') }}" class="btn-secondary inline-flex items-center justify-center">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-primary mb-4">Informations Personnelles</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-secondary mb-1">Nom & Prénom</p>
                                <p class="font-medium text-primary">{{ $user->full_name ?: ($user->nom . ' ' . $user->prénom) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-secondary mb-1">Numéro Dentiste</p>
                                <p class="font-medium text-primary">{{ $user->num_dentist ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-secondary mb-1">Email</p>
                                <p class="font-medium text-primary">{{ $user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-secondary mb-1">Statut du compte</p>
                                @if($user->approved_at)
                                    <p class="font-medium text-accent-secondary">
                                        Approuvé le {{ $user->approved_at->format('d/m/Y H:i') }}
                                    </p>
                                @else
                                    <p class="font-medium text-warning">En attente d'approbation</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-secondary mb-1">Téléphone</p>
                                <p class="font-medium text-primary">{{ $user->tél ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-secondary mb-1">Numéro Ordinaire</p>
                                <p class="font-medium text-primary">{{ $user->num_ordinaire ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($user->adresse || $user->ville || $user->gouvernorat)
                    <div>
                        <h3 class="text-lg font-semibold text-primary mb-4">Adresse</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-secondary mb-1">Gouvernorat</p>
                                <p class="font-medium text-primary">{{ $user->gouvernorat ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-secondary mb-1">Ville</p>
                                <p class="font-medium text-primary">{{ $user->ville ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-secondary mb-1">Adresse</p>
                                <p class="font-medium text-primary">{{ $user->adresse ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
