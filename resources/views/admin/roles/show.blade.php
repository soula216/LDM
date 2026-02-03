<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rôle : ') . ucfirst($role->name) }}
            </h2>
            <div class="flex flex-row gap-2">
                @can('manage_permissions')
                <a href="{{ route('admin.roles.edit', $role) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Modifier Permissions
                </a>
                @endcan
                <a href="{{ route('admin.roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Informations</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nom du Rôle</p>
                            <p class="font-semibold text-lg">{{ ucfirst($role->name) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nombre d'utilisateurs</p>
                            <p class="font-semibold text-lg">{{ $role->users()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Permissions</h3>
                    <div class="space-y-4">
                        @foreach($allPermissions as $category => $perms)
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2 capitalize">{{ $category }}</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($perms as $permission)
                                        <span class="px-3 py-1 text-sm rounded-full
                                            @if($permissions->contains($permission))
                                                bg-green-100 text-green-800
                                            @else
                                                bg-gray-100 text-gray-600
                                            @endif">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
