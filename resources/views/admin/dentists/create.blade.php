<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">
                    {{ __('Nouveau Dentiste') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card">
                <form action="{{ route('admin.dentists.store') }}" method="POST" class="space-y-6">
                    @csrf

                    @if(session('error'))
                        <div class="p-4 bg-danger/10 border-l-4 border-danger rounded-lg flex items-center">
                            <svg class="w-5 h-5 text-danger mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-danger font-medium text-sm sm:text-base">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <x-label for="nom" value="{{ __('Nom') }}" class="text-primary font-medium mb-2" />
                            <x-input id="nom" name="nom" type="text" class="block w-full input-field" required />
                            <x-input-error for="nom" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="prénom" value="{{ __('Prénom') }}" class="text-primary font-medium mb-2" />
                            <x-input id="prénom" name="prénom" type="text" class="block w-full input-field" required />
                            <x-input-error for="prénom" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="email" value="{{ __('Email') }}" class="text-primary font-medium mb-2" />
                            <x-input id="email" name="email" type="email" class="block w-full input-field" required />
                            <x-input-error for="email" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="tél" value="{{ __('Téléphone') }}" class="text-primary font-medium mb-2" />
                            <x-input id="tél" name="tél" type="tel" class="block w-full input-field" />
                            <x-input-error for="tél" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="gouvernorat" value="{{ __('Gouvernorat') }}" class="text-primary font-medium mb-2" />
                            <x-input id="gouvernorat" name="gouvernorat" type="text" class="block w-full input-field" />
                            <x-input-error for="gouvernorat" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="ville" value="{{ __('Ville') }}" class="text-primary font-medium mb-2" />
                            <x-input id="ville" name="ville" type="text" class="block w-full input-field" />
                            <x-input-error for="ville" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="num_ordinaire" value="{{ __('Numéro Ordinaire') }}" class="text-primary font-medium mb-2" />
                            <x-input id="num_ordinaire" name="num_ordinaire" type="text" class="block w-full input-field" />
                            <x-input-error for="num_ordinaire" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-label for="adresse" value="{{ __('Adresse') }}" class="text-primary font-medium mb-2" />
                        <textarea id="adresse" name="adresse" rows="3" class="block w-full input-field"></textarea>
                        <x-input-error for="adresse" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <x-label for="num_dentist" value="{{ __('Numéro Dentiste') }}" class="text-primary font-medium mb-2" />
                            <x-input id="num_dentist" name="num_dentist" type="number" class="block w-full input-field" />
                            <x-input-error for="num_dentist" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="order" value="{{ __('Ordre') }}" class="text-primary font-medium mb-2" />
                            <x-input id="order" name="order" type="number" class="block w-full input-field" />
                            <x-input-error for="order" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-4 border-t border-border">
                        <a href="{{ route('admin.dentists.index') }}" class="btn-secondary text-center sm:w-auto">
                            Annuler
                        </a>
                        <button type="submit" class="btn-primary w-full sm:w-auto">
                            Créer le dentiste
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
