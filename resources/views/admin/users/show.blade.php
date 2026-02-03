<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Utilisateur : ') . $user->full_name }}
            </h2>
            <div>
                @can('edit_users')
                <a href="{{ route('admin.users.edit', $user) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Modifier
                </a>
                @endcan
                <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Informations Personnelles</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Nom</p>
                                    <p class="font-semibold">{{ $user->nom }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Prénom</p>
                                    <p class="font-semibold">{{ $user->prénom }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-semibold">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Téléphone</p>
                                    <p class="font-semibold">{{ $user->tél ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Rôles & Permissions</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Rôles</p>
                                    <div class="mt-1">
                                        @foreach($user->getRoleNames() as $role)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-1">
                                                {{ $role }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Groupe</p>
                                    <p class="font-semibold">{{ $user->groupe->nom ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        @if($user->adresse || $user->ville || $user->gouvernorat)
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Adresse</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Gouvernorat</p>
                                    <p class="font-semibold">{{ $user->gouvernorat ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Ville</p>
                                    <p class="font-semibold">{{ $user->ville ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Adresse</p>
                                    <p class="font-semibold">{{ $user->adresse ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
