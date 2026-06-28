<div class="space-y-4 sm:space-y-6">
    @foreach($permissions as $category => $perms)
        <div class="card border-l-4 border-primary">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-primary rounded-lg">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-primary capitalize">{{ $category }}</h3>
                </div>
                <span class="px-2 sm:px-3 py-1 text-xs font-medium rounded-md bg-primary/10 text-primary border border-primary/20">
                    {{ $perms->count() }} permission(s)
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                @foreach($perms as $permission)
                    <div class="p-4 bg-neutral-100 rounded-lg border border-border hover:border-primary/30 transition-colors">
                        <div class="mb-3">
                            <span class="font-medium text-sm text-primary block">{{ $permission->name }}</span>
                        </div>
                        <div class="pt-3 border-t border-border">
                            <p class="text-xs font-medium text-secondary uppercase tracking-wider mb-2">Rôles associés :</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($permission->roles->where('name', '!=', 'admin') as $role)
                                    <span class="px-2 py-1 text-xs font-medium rounded-md bg-primary/10 text-primary border border-primary/20">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                                @if($permission->roles->where('name', '!=', 'admin')->isEmpty())
                                    <span class="text-xs text-secondary italic">Aucun rôle</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
