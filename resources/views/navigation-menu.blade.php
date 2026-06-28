<div>
{{-- Overlay mobile --}}
<div
    x-show="sidebarOpen"
    x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden"
    style="display: none;"
    aria-hidden="true"
></div>

{{-- Barre mobile --}}
<div class="lg:hidden fixed top-0 left-0 right-0 z-30 h-16 bg-card/95 backdrop-blur-md border-b border-border flex items-center justify-between px-4">
    <button
        type="button"
        @click="sidebarOpen = !sidebarOpen"
        class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-primary hover:bg-neutral-100 transition-colors"
        aria-label="Menu"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    <a href="{{ route('dashboard') }}" class="flex items-center">
        <x-application-mark class="block h-10 w-auto" />
    </a>
    <div class="w-10"></div>
</div>

{{-- Sidebar --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="app-sidebar fixed top-0 left-0 z-50 h-full w-[17.5rem] flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0"
>
    {{-- Brand --}}
    <div class="sidebar-brand px-5 pt-6 pb-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group" @click="sidebarOpen = false">
            <x-application-mark class="block h-14 w-auto object-contain" />
        </a>
        <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Espace client</p>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 pb-4 space-y-6">
        <div>
            <p class="sidebar-section-title pt-4">Principal</p>
            <div class="space-y-1">
                <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" @click="sidebarOpen = false">
                    <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>{{ __('Dashboard') }}</span>
                </x-sidebar-link>

                @can('view_commandes_calendar')
                <x-sidebar-link href="{{ route('app.commandes.calendar') }}" :active="request()->routeIs('app.commandes.*')" @click="sidebarOpen = false">
                    <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>{{ __('Calendrier') }}</span>
                </x-sidebar-link>
                @endcan
            </div>
        </div>

        <div>
            <p class="sidebar-section-title">Gestion</p>
            <div class="space-y-1">
                @can('view_commandes')
                <x-sidebar-link href="{{ route('admin.commandes.index') }}" :active="request()->routeIs('admin.commandes.*')" @click="sidebarOpen = false">
                    <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span>{{ __('Commandes') }}</span>
                </x-sidebar-link>
                @endcan

                @can('view_factures')
                <x-sidebar-link href="{{ route('admin.factures.index') }}" :active="request()->routeIs('admin.factures.*')" @click="sidebarOpen = false">
                    <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>{{ __('Factures') }}</span>
                </x-sidebar-link>
                @endcan

                @can('manage_service_pricing')
                <x-sidebar-link href="{{ route('admin.services.index') }}" :active="request()->routeIs('admin.services.*')" @click="sidebarOpen = false">
                    <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>{{ __('Services') }}</span>
                </x-sidebar-link>
                @endcan

                @if(auth()->user()?->hasRole('admin'))
                <x-sidebar-link href="{{ route('admin.depenses.index') }}" :active="request()->routeIs('admin.depenses.*')" @click="sidebarOpen = false">
                    <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ __('Dépenses') }}</span>
                </x-sidebar-link>
                @endif
            </div>
        </div>

        @can('view_users')
        <div>
            <p class="sidebar-section-title">Utilisateurs</p>
            <div class="space-y-1">
                <x-sidebar-link href="{{ route('admin.dentists.index') }}" :active="request()->routeIs('admin.dentists.*')" @click="sidebarOpen = false">
                    <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>{{ __('Dentistes') }}</span>
                </x-sidebar-link>

                <x-sidebar-link href="{{ route('admin.teams.index') }}" :active="request()->routeIs('admin.teams.*')" @click="sidebarOpen = false">
                    <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>{{ __('Équipes') }}</span>
                </x-sidebar-link>
            </div>
        </div>
        @endcan

        @if(auth()->user()?->hasRole('admin'))
        <div>
            <p class="sidebar-section-title">Administration</p>
            <div class="space-y-1">
                <x-sidebar-link href="{{ route('admin.config.index') }}" :active="request()->routeIs('admin.config.*') || request()->routeIs('admin.groupes.*') || request()->routeIs('admin.criteres-quality.*')" @click="sidebarOpen = false">
                    <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>{{ __('Configuration') }}</span>
                </x-sidebar-link>

                @if(auth()->user()->can('view_roles') || auth()->user()->can('manage_permissions'))
                <x-sidebar-link href="{{ route('admin.roles.index') }}" :active="request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*')" @click="sidebarOpen = false">
                    <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>{{ __('Rôles / Permissions') }}</span>
                </x-sidebar-link>
                @endif
            </div>
        </div>
        @endif
    </nav>

    {{-- Footer sidebar --}}
    <div class="sidebar-footer p-3 space-y-2 border-t border-white/10">
        <a href="{{ route('vitrine') }}" target="_blank" rel="noopener noreferrer" class="sidebar-link sidebar-link-external">
            <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            <span>{{ __('Site vitrine') }}</span>
        </a>

        <div x-data="{ userMenuOpen: false }" class="relative">
            <button
                type="button"
                @click="userMenuOpen = !userMenuOpen"
                @click.outside="userMenuOpen = false"
                class="sidebar-user-btn w-full"
            >
                <div class="sidebar-user-avatar">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="w-full h-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    @else
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0 text-left">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->full_name ?: Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                </div>
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div
                x-show="userMenuOpen"
                x-transition
                class="absolute bottom-full left-0 right-0 mb-2 bg-slate-800 border border-white/10 rounded-xl shadow-xl overflow-hidden py-1"
                style="display: none;"
            >
                <a href="{{ route('profile.show') }}" class="sidebar-dropdown-link">{{ __('Profile') }}</a>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                <a href="{{ route('api-tokens.index') }}" class="sidebar-dropdown-link">{{ __('API Tokens') }}</a>
                @endif

                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures() && Auth::user()->currentTeam)
                <div class="border-t border-white/10 my-1"></div>
                @if(Auth::user()->currentTeam)
                <a href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" class="sidebar-dropdown-link">{{ __('Team Settings') }}</a>
                @endif
                @endif

                <div class="border-t border-white/10 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-dropdown-link w-full text-left text-red-300 hover:text-red-200 hover:bg-red-500/10">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
</div>
