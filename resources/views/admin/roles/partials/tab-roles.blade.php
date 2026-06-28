<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
    @foreach($roles as $role)
        <div class="card hover:shadow-md transition-all duration-200 border-l-4 border-accent">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-accent rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-primary">{{ ucfirst($role->name) }}</h3>
                        <p class="text-xs sm:text-sm text-secondary">{{ $role->permissions->count() }} permissions</p>
                    </div>
                </div>
                <span class="px-2 sm:px-3 py-1 text-xs font-medium rounded-md bg-primary/10 text-primary border border-primary/20">
                    {{ $role->users_count }} utilisateur(s)
                </span>
            </div>

            <div class="mb-6">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider mb-3">Permissions :</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($role->permissions->take(4) as $permission)
                        <span class="px-2 py-1 text-xs font-medium rounded-md bg-neutral-100 text-secondary border border-border">
                            {{ $permission->name }}
                        </span>
                    @endforeach
                    @if($role->permissions->count() > 4)
                        <span class="px-2 py-1 text-xs font-medium rounded-md bg-neutral-100 text-secondary border border-border">
                            +{{ $role->permissions->count() - 4 }} autres
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('admin.roles.show', $role) }}" class="flex-1 btn-secondary text-center text-sm">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Voir
                </a>
                @can('manage_permissions')
                <a href="{{ route('admin.roles.edit', $role) }}" class="flex-1 btn-primary text-center text-sm">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
                @if($role->users_count == 0)
                <button
                    type="button"
                    @click="showDeleteModal = true; roleToDelete = '{{ ucfirst($role->name) }}'; deleteFormAction = '{{ route('admin.roles.destroy', $role) }}'"
                    class="flex-1 text-center text-sm text-white rounded-lg px-4 py-2 font-medium transition-colors duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    style="background-color: #EF4444;"
                    onmouseover="this.style.backgroundColor='#DC2626'"
                    onmouseout="this.style.backgroundColor='#EF4444'"
                >
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Supprimer
                </button>
                @endif
                @endcan
            </div>
        </div>
    @endforeach
</div>
