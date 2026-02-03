<nav x-data="{ open: false }" class="bg-card border-b border-border shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16">
            <div class="flex items-center flex-1 justify-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-20 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-md bg-gradient-to-br from-primary to-accent-secondary flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                            </div>
                            <span>{{ __('Dashboard') }}</span>
                        </div>
                    </x-nav-link>

                    @can('view_commandes_calendar')
                    <x-nav-link href="{{ route('app.commandes.calendar') }}" :active="request()->routeIs('app.commandes.*')">
                        {{ __('Calendrier') }}
                    </x-nav-link>
                    @endcan

                    @can('manage_service_pricing')
                    <x-nav-link href="{{ route('admin.services.index') }}" :active="request()->routeIs('admin.services.*')">
                        {{ __('Services') }}
                    </x-nav-link>
                    @endcan

                    {{-- Commandes - Accessible avec view_commandes --}}
                    @can('view_commandes')
                    <x-nav-link href="{{ route('admin.commandes.index') }}" :active="request()->routeIs('admin.commandes.*')">
                        {{ __('Commandes') }}
                    </x-nav-link>
                    @endcan

                    {{-- Factures - Accessible avec view_factures --}}
                    @can('view_factures')
                    <x-nav-link href="{{ route('admin.factures.index') }}" :active="request()->routeIs('admin.factures.*')">
                        {{ __('Factures') }}
                    </x-nav-link>
                    @endcan

                    {{-- Dentistes - Accessible avec view_users --}}
                    @can('view_users')
                    <x-nav-link href="{{ route('admin.dentists.index') }}" :active="request()->routeIs('admin.dentists.*')">
                        {{ __('Dentistes') }}
                    </x-nav-link>
                    @endcan

                    {{-- Équipes - Accessible avec view_users --}}
                    @can('view_users')
                    <x-nav-link href="{{ route('admin.teams.index') }}" :active="request()->routeIs('admin.teams.*')">
                        {{ __('Équipes') }}
                    </x-nav-link>
                    @endcan

                    {{-- Config - Uniquement pour les admins --}}
                    @if(auth()->user()?->hasRole('admin'))
                    <x-nav-link href="{{ route('admin.config.index') }}" :active="request()->routeIs('admin.config.*') || request()->routeIs('admin.groupes.*') || request()->routeIs('admin.criteres-quality.*')">
                        {{ __('Configuration') }}
                    </x-nav-link>
                    @endif

                    {{-- Admin Menu - Uniquement pour les admins --}}
                    @if(auth()->user()?->hasRole('admin'))
                        @can('view_roles')
                        <x-nav-link href="{{ route('admin.roles.index') }}" :active="request()->routeIs('admin.roles.*')">
                            {{ __('Rôles') }}
                        </x-nav-link>
                        @endcan
                        @can('manage_permissions')
                        <x-nav-link href="{{ route('admin.permissions.index') }}" :active="request()->routeIs('admin.permissions.*')">
                            {{ __('Permissions') }}
                        </x-nav-link>
                        @endcan
                    @endif

                    <!-- Bloc utilisateur à droite (après le lien Permissions) -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6 ml-auto min-w-[280px]">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures() && Auth::user()->currentTeam)
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->currentTeam->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    @if(Auth::user()->currentTeam)
                                        <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                            {{ __('Team Settings') }}
                                        </x-dropdown-link>
                                    @endif

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-border"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex items-center text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-5 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150 min-w-[260px] justify-between whitespace-nowrap">
                                        <div class="flex items-center min-w-0">
                                            <div class="w-8 h-8 mr-2 flex-shrink-0 rounded-full bg-gradient-to-br from-primary to-accent-secondary flex items-center justify-center shadow-sm">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <span class="truncate">{{ Auth::user()->full_name ?: Auth::user()->name }}</span>
                                        </div>
                                        <svg class="ms-2 -me-0.5 size-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-secondary">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-border"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-secondary hover:text-primary hover:bg-neutral-100 focus:outline-none focus:bg-neutral-100 focus:text-primary transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('view_commandes_calendar')
            <x-responsive-nav-link href="{{ route('app.commandes.calendar') }}" :active="request()->routeIs('app.commandes.*')">
                {{ __('Calendrier') }}
            </x-responsive-nav-link>
            @endcan

            @can('manage_service_pricing')
            <x-responsive-nav-link href="{{ route('admin.services.index') }}" :active="request()->routeIs('admin.services.*')">
                {{ __('Services') }}
            </x-responsive-nav-link>
            @endcan

            {{-- Commandes - Accessible avec view_commandes --}}
            @can('view_commandes')
            <x-responsive-nav-link href="{{ route('admin.commandes.index') }}" :active="request()->routeIs('admin.commandes.*')">
                {{ __('Commandes') }}
            </x-responsive-nav-link>
            @endcan

            {{-- Factures - Accessible avec view_factures --}}
            @can('view_factures')
            <x-responsive-nav-link href="{{ route('admin.factures.index') }}" :active="request()->routeIs('admin.factures.*')">
                {{ __('Factures') }}
            </x-responsive-nav-link>
            @endcan

            {{-- Dentistes - Accessible avec view_users --}}
            @can('view_users')
            <x-responsive-nav-link href="{{ route('admin.dentists.index') }}" :active="request()->routeIs('admin.dentists.*')">
                {{ __('Dentistes') }}
            </x-responsive-nav-link>
            @endcan

            {{-- Équipes - Accessible avec view_users --}}
            @can('view_users')
            <x-responsive-nav-link href="{{ route('admin.teams.index') }}" :active="request()->routeIs('admin.teams.*')">
                {{ __('Équipes') }}
            </x-responsive-nav-link>
            @endcan

            {{-- Config - Uniquement pour les admins --}}
            @if(auth()->user()?->hasRole('admin'))
            <x-responsive-nav-link href="{{ route('admin.config.index') }}" :active="request()->routeIs('admin.config.*') || request()->routeIs('admin.groupes.*') || request()->routeIs('admin.criteres-quality.*')">
                {{ __('Configuration') }}
            </x-responsive-nav-link>
            @endif

            {{-- Admin Menu - Uniquement pour les admins --}}
            @if(auth()->user()?->hasRole('admin'))
                @can('view_roles')
                <x-responsive-nav-link href="{{ route('admin.roles.index') }}" :active="request()->routeIs('admin.roles.*')">
                    {{ __('Rôles') }}
                </x-responsive-nav-link>
                @endcan
                @can('manage_permissions')
                <x-responsive-nav-link href="{{ route('admin.permissions.index') }}" :active="request()->routeIs('admin.permissions.*')">
                    {{ __('Permissions') }}
                </x-responsive-nav-link>
                @endcan
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-border">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures() && Auth::user()->currentTeam)
                    <div class="border-t border-border"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    @if(Auth::user()->currentTeam)
                        <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                            {{ __('Team Settings') }}
                        </x-responsive-nav-link>
                    @endif

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-border"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>

                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</nav>
