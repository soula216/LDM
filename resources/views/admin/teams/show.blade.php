<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Membre d\'équipe : ') . $user->full_name }}
                </h2>
            </div>
            <div class="flex flex-row gap-2">
                @can('edit_users')
                <a href="{{ route('admin.teams.edit', $user) }}" class="btn-primary inline-flex items-center justify-center" style="background-color: #F59E0B; border-color: #F59E0B;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
                @endcan
                <a href="{{ route('admin.teams.index') }}" class="btn-secondary inline-flex items-center justify-center">
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
                                <p class="text-sm text-secondary mb-1">Email</p>
                                <p class="font-medium text-primary">{{ $user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-secondary mb-1">Téléphone</p>
                                <p class="font-medium text-primary">{{ $user->tél ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-secondary mb-1">Rôle</p>
                                <div class="mt-1">
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @if($user->hasRole('employer') && $user->groupe)
                            <div>
                                <p class="text-sm text-secondary mb-1">Groupe</p>
                                <p class="font-medium text-primary">{{ $user->groupe->nom }}</p>
                            </div>
                            @endif
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
