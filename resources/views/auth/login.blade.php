<x-guest-layout>
    <x-authentication-card>
        <form method="POST" action="{{ route('login') }}" class="login-form-new" novalidate>
            @csrf

            <x-validation-errors class="mb-4" />

            @session('status')
                <div class="mb-4 font-medium text-sm text-green-400">
                    {{ $value }}
                </div>
            @endsession

            <div class="login-input-group login-animate-slide-up">
                <label for="email">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="Email">
                <div class="login-input-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                </div>
            </div>

            <div class="login-input-group login-animate-slide-up" style="--delay: 0.1s;">
                <label for="password">Mot de passe</label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••••••">
                <div class="login-input-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <button 
                    type="button"
                    id="togglePassword"
                    class="login-toggle-password absolute z-10 flex items-center justify-center transition-all duration-200 focus:outline-none rounded-md"
                    aria-label="Afficher/Masquer le mot de passe">
                    <!-- Eye icon (show) -->
                    <svg id="eyeIcon" 
                         class="w-6 h-6 transition-opacity duration-200" 
                         fill="none" 
                         stroke="#00d4aa" 
                         stroke-width="3"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <!-- Eye slash icon (hide) -->
                    <svg id="eyeSlashIcon" 
                         class="w-6 h-6 transition-opacity duration-200 hidden" 
                         fill="none" 
                         stroke="#00d4aa" 
                         stroke-width="3"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>
                </button>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const togglePassword = document.getElementById('togglePassword');
                    const passwordInput = document.getElementById('password');
                    const eyeIcon = document.getElementById('eyeIcon');
                    const eyeSlashIcon = document.getElementById('eyeSlashIcon');

                    if (togglePassword && passwordInput && eyeIcon && eyeSlashIcon) {
                        togglePassword.addEventListener('click', function() {
                            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                            passwordInput.setAttribute('type', type);

                            if (type === 'password') {
                                eyeIcon.classList.remove('hidden');
                                eyeSlashIcon.classList.add('hidden');
                            } else {
                                eyeIcon.classList.add('hidden');
                                eyeSlashIcon.classList.remove('hidden');
                            }
                        });
                    }
                });
            </script>

            <div class="login-form-options">
                <label class="login-checkbox-container">
                    <input type="checkbox" id="remember_me" name="remember">
                    <span class="login-checkmark"></span>
                    Se souvenir de moi
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="login-forgot-link">Mot de passe oublié ?</a>
                @endif
            </div>

            <button type="submit" class="login-btn-gradient login-animate-scale">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Se connecter
                </span>
                <div class="login-btn-shine"></div>
            </button>
        </form>
    </x-authentication-card>
</x-guest-layout>
