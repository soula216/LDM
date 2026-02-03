<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier Permissions : ') . ucfirst($role->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="p-6">
                    @csrf
                    @method('PATCH')

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="space-y-6">
                        @foreach($allPermissions as $category => $perms)
                            <div class="border rounded-lg p-4">
                                <h3 class="font-semibold text-gray-800 mb-3 capitalize flex items-center">
                                    <input type="checkbox" 
                                           class="category-checkbox mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                           data-category="{{ $category }}">
                                    {{ $category }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($perms as $permission)
                                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   class="permission-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                   data-category="{{ $category }}"
                                                   {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-4">
                            Annuler
                        </a>
                        <x-button>
                            {{ __('Mettre à jour') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Sélection/désélection par catégorie
        document.querySelectorAll('.category-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const category = this.dataset.category;
                document.querySelectorAll(`.permission-checkbox[data-category="${category}"]`).forEach(perm => {
                    perm.checked = this.checked;
                });
            });
        });
    </script>
</x-app-layout>
