<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-gradient-to-br from-primary to-primary-dark rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Tableau de Bord') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-6 sm:mb-8">
                <div class="card bg-gradient-to-br from-primary via-primary to-primary-dark text-white overflow-hidden relative">
                    <!-- Decorative gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent opacity-50"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex-1">
                                <h1 class="text-xl sm:text-2xl font-semibold mb-2">Bienvenue, {{ auth()->user()->full_name ?? auth()->user()->name }} !</h1>
                                <p class="text-white/90 text-sm sm:text-base">Gérez votre laboratoire dentaire en toute simplicité</p>
                            </div>
                            <div class="hidden md:block flex-shrink-0">
                                <svg class="w-16 h-16 lg:w-20 lg:h-20 text-white opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 sm:mt-8">
                <h2 class="text-lg sm:text-xl font-semibold text-primary mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Actions Rapides
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @can('view_commandes_calendar')
                    <a href="{{ route('app.commandes.calendar') }}" class="card hover:shadow-md transition-all duration-200 border-l-4 group bg-gradient-to-br from-card via-neutral-50 to-card card-blue">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br rounded-lg icon-blue">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <svg class="w-5 h-5 text-secondary arrow-icon transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">Calendrier Commandes</h3>
                        <p class="text-xs sm:text-sm text-secondary">Visualiser les commandes dans un calendrier</p>
                    </a>
                    @endcan

                    @can('view_commandes')
                    <a href="{{ route('admin.commandes.index') }}" class="card hover:shadow-md transition-all duration-200 border-l-4 border-primary group bg-gradient-to-br from-card via-neutral-50 to-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br from-primary to-primary-dark rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <svg class="w-5 h-5 text-secondary group-hover:text-primary transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">Commandes</h3>
                        <p class="text-xs sm:text-sm text-secondary">Gérer et suivre toutes les commandes</p>
                    </a>
                    @endcan

                    @can('view_factures')
                    <a href="{{ route('admin.factures.index') }}" class="card hover:shadow-md transition-all duration-200 border-l-4 group bg-gradient-to-br from-card via-neutral-50 to-card card-warning">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br rounded-lg icon-warning">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <svg class="w-5 h-5 text-secondary arrow-icon transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">Factures</h3>
                        <p class="text-xs sm:text-sm text-secondary">Gérer et consulter toutes les factures</p>
                    </a>
                    @endcan

                    @can('view_users')
                    <a href="{{ route('admin.users.index') }}" class="card hover:shadow-md transition-all duration-200 border-l-4 group bg-gradient-to-br from-card via-neutral-50 to-card card-green">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br rounded-lg icon-green">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <svg class="w-5 h-5 text-secondary arrow-icon transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">Utilisateurs</h3>
                        <p class="text-xs sm:text-sm text-secondary">Gérer les utilisateurs et leurs permissions</p>
                    </a>
                    @endcan

                    @can('view_roles')
                    <a href="{{ route('admin.roles.index') }}" class="card hover:shadow-md transition-all duration-200 border-l-4 group bg-gradient-to-br from-card via-neutral-50 to-card card-purple">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br rounded-lg icon-purple">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <svg class="w-5 h-5 text-secondary arrow-icon transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">Rôles & Permissions</h3>
                        <p class="text-xs sm:text-sm text-secondary">Configurer les rôles et permissions</p>
                    </a>
                    @endcan

                    @can('manage_service_pricing')
                    <a href="{{ route('admin.services.index') }}" class="card hover:shadow-md transition-all duration-200 border-l-4 group bg-gradient-to-br from-card via-neutral-50 to-card card-cyan">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br rounded-lg icon-cyan">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <svg class="w-5 h-5 text-secondary arrow-icon transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">Services</h3>
                        <p class="text-xs sm:text-sm text-secondary">Gérer les prix des services</p>
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Stats Cards - Affichées seulement si l'utilisateur a la permission view_statistics (admin et responsable) -->
            @can('view_statistics')
            @if(isset($stats))
            <div class="mt-6 sm:mt-8" x-data="{ statsOpen: false }">
                <div class="card">
                    <button @click="statsOpen = !statsOpen" class="w-full flex items-center justify-between p-4 hover:bg-neutral-50 transition-colors duration-200 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-gradient-to-br from-primary to-primary-dark rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <h2 class="text-lg sm:text-xl font-semibold text-primary">
                                    Statistiques Générales
                                </h2>
                                <p class="text-xs sm:text-sm text-secondary mt-1">Vue d'ensemble de votre système</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-primary transition-transform duration-200" :class="{ 'rotate-180': statsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="statsOpen" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-[2000px]"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 max-h-[2000px]"
                         x-transition:leave-end="opacity-0 max-h-0"
                         class="overflow-hidden">
                        <div class="px-4 pb-4 pt-2">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <!-- Total équipes -->
                        <div class="card bg-gradient-to-br from-primary via-primary to-primary-dark text-white hover:shadow-md transition-all duration-200 overflow-hidden relative">
                            <!-- Decorative gradient overlay -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent opacity-50"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs sm:text-sm font-medium mb-2 text-white/90">Total équipes</p>
                                <p class="text-3xl sm:text-4xl font-semibold mb-1">{{ $stats['total_teams'] ?? 0 }}</p>
                                <p class="text-xs text-white/75 hidden sm:block">Tous les membres d'équipe</p>
                            </div>
                        </div>

                        <!-- Total Dentists -->
                        <div class="card bg-gradient-to-br from-accent-secondary via-accent-secondary to-green-600 text-white hover:shadow-md transition-all duration-200 overflow-hidden relative">
                            <!-- Decorative gradient overlay -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent opacity-50"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs sm:text-sm font-medium mb-2 text-white/90">Total Dentists</p>
                                <p class="text-3xl sm:text-4xl font-semibold mb-1">{{ $stats['total_dentists'] ?? 0 }}</p>
                                <p class="text-xs text-white/75 hidden sm:block">Tous les dentistes</p>
                            </div>
                        </div>

                        <!-- Total Roles -->
                        <div class="card bg-gradient-to-br from-accent via-accent to-neutral-900 text-white hover:shadow-md transition-all duration-200 overflow-hidden relative">
                            <!-- Decorative gradient overlay -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent opacity-50"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs sm:text-sm font-medium mb-2 text-white/90">Total Rôles</p>
                                <p class="text-3xl sm:text-4xl font-semibold mb-1">{{ $stats['total_roles'] ?? 0 }}</p>
                                <p class="text-xs text-white/75 hidden sm:block">Rôles définis dans le système</p>
                            </div>
                        </div>

                        <!-- Total Permissions -->
                        <div class="card bg-gradient-to-br from-card via-neutral-50 to-card border-2 border-primary text-primary hover:shadow-md transition-all duration-200 overflow-hidden relative">
                            <!-- Decorative gradient overlay -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-primary/5 to-transparent opacity-50"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-gradient-to-br from-primary/10 via-primary/15 to-primary/10 rounded-lg">
                                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs sm:text-sm font-medium mb-2 text-primary">Total Permissions</p>
                                <p class="text-3xl sm:text-4xl font-semibold mb-1 text-primary">{{ $stats['total_permissions'] ?? 0 }}</p>
                                <p class="text-xs text-secondary hidden sm:block">Permissions disponibles</p>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endcan
        </div>
    </div>

    <style>
        /* Card borders */
        .card-blue {
            border-left-color: #3B82F6 !important;
        }
        
        .card-warning {
            border-left-color: #F59E0B !important;
        }
        
        .card-green {
            border-left-color: #22C55E !important;
        }
        
        .card-purple {
            border-left-color: #A855F7 !important;
        }
        
        .card-cyan {
            border-left-color: #06B6D4 !important;
        }
        
        /* Icon backgrounds */
        .icon-blue {
            background: linear-gradient(to bottom right, #3B82F6, #2563EB) !important;
        }
        
        .icon-warning {
            background: linear-gradient(to bottom right, #F59E0B, #D97706) !important;
        }
        
        .icon-green {
            background: linear-gradient(to bottom right, #22C55E, #16A34A) !important;
        }
        
        .icon-purple {
            background: linear-gradient(to bottom right, #A855F7, #9333EA) !important;
        }
        
        .icon-cyan {
            background: linear-gradient(to bottom right, #06B6D4, #0891B2) !important;
        }
        
        /* Arrow hover effects */
        .card-blue:hover .arrow-icon {
            color: #3B82F6 !important;
        }
        
        .card-warning:hover .arrow-icon {
            color: #F59E0B !important;
        }
        
        .card-green:hover .arrow-icon {
            color: #22C55E !important;
        }
        
        .card-purple:hover .arrow-icon {
            color: #A855F7 !important;
        }
        
        .card-cyan:hover .arrow-icon {
            color: #06B6D4 !important;
        }
    </style>
</x-app-layout>
