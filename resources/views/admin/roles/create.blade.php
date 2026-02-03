<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer un Nouveau Rôle') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <form action="{{ route('admin.roles.store') }}" method="POST" class="p-6">
                    @csrf

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Nom du rôle -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du rôle <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Ex: manager, assistant, etc.">
                        <p class="mt-1 text-sm text-gray-500">Le nom doit être unique et ne peut pas être "admin".</p>
                    </div>

                    <!-- Permissions -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            Permissions
                        </label>
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
                                                       data-category="{{ $category }}">
                                                <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-4">
                            Annuler
                        </a>
                        <x-button>
                            {{ __('Créer le Rôle') }}
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
