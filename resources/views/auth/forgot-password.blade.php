<x-guest-layout>
    <x-authentication-card>
        <form method="POST" action="{{ route('password.email') }}" class="login-form-new" novalidate>
            @csrf

            <x-validation-errors class="mb-4" />

            @session('status')
                <div class="mb-4 font-medium text-sm text-green-400">
                    {{ $value }}
                </div>
            @endsession

            <div class="mb-4 text-sm" style="color: rgba(255, 255, 255, 0.9);">
                {{ __('Mot de passe oublié ? Aucun problème. Indiquez-nous simplement votre adresse e-mail et nous vous enverrons un lien de réinitialisation qui vous permettra d\'en choisir un nouveau.') }}
            </div>

            <div class="login-input-group login-animate-slide-up">
                <label for="email" style="color: rgba(255, 255, 255, 0.95);">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="votre@email.com">
                <div class="login-input-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                </div>
            </div>

            <button type="submit" class="login-btn-gradient login-animate-scale">
                <span>Envoyer le lien de réinitialisation</span>
                <div class="login-btn-shine"></div>
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="login-forgot-link">
                {{ __('← Retour à la connexion') }}
            </a>
        </div>
    </x-authentication-card>
</x-guest-layout>
