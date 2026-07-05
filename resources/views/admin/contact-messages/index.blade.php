<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-primary rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-primary">{{ __('Messages contact') }}</h2>
            </div>
            <div class="px-4 py-2 bg-primary/10 border border-primary/20 rounded-xl">
                <p class="text-xs text-secondary uppercase tracking-wide font-medium">Total</p>
                <p class="text-lg font-bold text-primary">{{ $messages->total() }}</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{ showDeleteModal: false, deleteFormAction: '' }" x-cloak class="py-4 sm:py-8 bg-app min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-accent-secondary/10 border-l-4 border-accent-secondary rounded-lg">
                    <span class="text-accent-secondary font-medium text-sm sm:text-base">{{ session('success') }}</span>
                </div>
            @endif

            <div class="card mb-6">
                <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Rechercher par nom, email, téléphone ou message…"
                        class="input-field flex-1"
                    >
                    <button type="submit" class="btn-primary whitespace-nowrap">Rechercher</button>
                    @if(request('search'))
                        <a href="{{ route('admin.contact-messages.index') }}" class="btn-secondary whitespace-nowrap text-center">Réinitialiser</a>
                    @endif
                </form>
            </div>

            <div class="card">
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-100">
                            <tr>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Date</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Nom</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden sm:table-cell">Email</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider hidden md:table-cell">Téléphone</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Message</th>
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-primary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-card divide-y divide-border">
                            @forelse($messages as $message)
                                <tr class="hover:bg-neutral-100/50 transition-colors">
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-secondary">
                                        {{ $message->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <div class="text-sm font-medium text-primary">{{ $message->name }}</div>
                                        <div class="text-xs text-secondary sm:hidden mt-1">{{ $message->email }}</div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <a href="mailto:{{ $message->email }}" class="text-sm text-primary hover:underline">{{ $message->email }}</a>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell text-sm text-secondary">
                                        {{ $message->phone ?: '—' }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-4">
                                        <p class="text-sm text-secondary max-w-md whitespace-pre-wrap">{{ Str::limit($message->message, 120) }}</p>
                                    </td>
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm">
                                        <button
                                            type="button"
                                            @click="showDeleteModal = true; deleteFormAction = '{{ route('admin.contact-messages.destroy', $message) }}'"
                                            class="text-danger hover:text-danger/80 transition-colors"
                                            title="Supprimer"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-secondary">
                                        Aucun message reçu pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($messages->hasPages())
                    <div class="mt-6 px-4 sm:px-0">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div
            x-show="showDeleteModal"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div class="bg-card rounded-xl shadow-xl max-w-md w-full p-6 border border-border" @click.outside="showDeleteModal = false">
                <h3 class="text-lg font-semibold text-primary mb-2">Supprimer ce message ?</h3>
                <p class="text-sm text-secondary mb-6">Cette action est irréversible.</p>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showDeleteModal = false" class="btn-secondary">Annuler</button>
                    <form :action="deleteFormAction" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
