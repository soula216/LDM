<x-guest-layout>
    <x-authentication-card
        wide
        title="Créer un compte"
        subtitle="Inscription réservée aux dentistes"
    >
        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register.store') }}" class="login-form-new login-form-register" novalidate>
            @csrf

            <div class="register-form-blocks">
                <section class="register-form-block">
                    <header class="register-form-block__header">
                        <span class="register-form-block__badge">1</span>
                        <div>
                            <h2 class="register-form-block__title">Identité & contact</h2>
                            <p class="register-form-block__subtitle">Vos informations personnelles</p>
                        </div>
                    </header>
                    <div class="register-form-block__fields">
                        <div class="login-input-group login-input-group--compact">
                            <label for="register-nom">Nom <span class="login-required">*</span></label>
                            <input id="register-nom" type="text" name="nom" value="{{ old('nom') }}" required autofocus autocomplete="family-name" placeholder="Nom" @class(['login-input--invalid' => $errors->has('nom')])>
                        </div>
                        <div class="login-input-group login-input-group--compact">
                            <label for="register-prenom">Prénom <span class="login-required">*</span></label>
                            <input id="register-prenom" type="text" name="prénom" value="{{ old('prénom') }}" required autocomplete="given-name" placeholder="Prénom" @class(['login-input--invalid' => $errors->has('prénom')])>
                        </div>
                        <div class="login-input-group login-input-group--compact">
                            <label for="register-email">Email <span class="login-required">*</span></label>
                            <input id="register-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email" @class(['login-input--invalid' => $errors->has('email')])>
                        </div>
                        <div class="login-input-group login-input-group--compact">
                            <label for="register-tel">Téléphone</label>
                            <input id="register-tel" type="tel" name="tél" value="{{ old('tél') }}" autocomplete="tel" placeholder="Téléphone" @class(['login-input--invalid' => $errors->has('tél')])>
                        </div>
                    </div>
                </section>

                <section class="register-form-block">
                    <header class="register-form-block__header">
                        <span class="register-form-block__badge register-form-block__badge--alt">2</span>
                        <div>
                            <h2 class="register-form-block__title">Localisation & accès</h2>
                            <p class="register-form-block__subtitle">Adresse et mot de passe</p>
                        </div>
                    </header>
                    <div class="register-form-block__fields">
                        <div class="login-input-group login-input-group--compact">
                            <label for="register-gouvernorat">Gouvernorat</label>
                            <input id="register-gouvernorat" type="text" name="gouvernorat" value="{{ old('gouvernorat') }}" placeholder="Gouvernorat" @class(['login-input--invalid' => $errors->has('gouvernorat')])>
                        </div>
                        <div class="login-input-group login-input-group--compact">
                            <label for="register-ville">Ville</label>
                            <input id="register-ville" type="text" name="ville" value="{{ old('ville') }}" placeholder="Ville" @class(['login-input--invalid' => $errors->has('ville')])>
                        </div>
                        <div class="login-input-group login-input-group--compact register-form-block__full">
                            <label for="register-adresse">Adresse</label>
                            <textarea id="register-adresse" name="adresse" rows="2" @class(['login-textarea', 'login-input--invalid' => $errors->has('adresse')]) placeholder="Adresse">{{ old('adresse') }}</textarea>
                        </div>
                        <div class="login-input-group login-input-group--compact">
                            <label for="register-password">Mot de passe <span class="login-required">*</span></label>
                            <input id="register-password" type="password" name="password" required autocomplete="new-password" placeholder="Min. 8 caractères" @class(['login-input--invalid' => $errors->has('password')])>
                            <button type="button" data-toggle-password="register-password" class="login-toggle-password login-toggle-password--compact absolute z-10 flex items-center justify-center focus:outline-none rounded-md" aria-label="Afficher/Masquer le mot de passe">
                                <svg data-eye-icon class="w-5 h-5" fill="none" stroke="#00d4aa" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg data-eye-slash-icon class="w-5 h-5 hidden" fill="none" stroke="#00d4aa" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        <div class="login-input-group login-input-group--compact">
                            <label for="register-password-confirmation">Confirmer <span class="login-required">*</span></label>
                            <input id="register-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmer" @class(['login-input--invalid' => $errors->has('password_confirmation')])>
                            <button type="button" data-toggle-password="register-password-confirmation" class="login-toggle-password login-toggle-password--compact absolute z-10 flex items-center justify-center focus:outline-none rounded-md" aria-label="Afficher/Masquer le mot de passe">
                                <svg data-eye-icon class="w-5 h-5" fill="none" stroke="#00d4aa" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg data-eye-slash-icon class="w-5 h-5 hidden" fill="none" stroke="#00d4aa" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <button type="submit" class="login-btn-gradient login-animate-scale">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Créer mon compte
                </span>
            </button>

            <div class="login-register-cta">
                <div class="login-register-cta__divider" aria-hidden="true">
                    <span>ou</span>
                </div>
                <p class="login-register-cta__label">Déjà inscrit ?</p>
                <a href="{{ route('login') }}" class="login-register-cta__btn login-register-cta__btn--alt">Se connecter</a>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('[data-toggle-password]').forEach(function(toggle) {
                    const inputId = toggle.getAttribute('data-toggle-password');
                    const passwordInput = document.getElementById(inputId);
                    const eyeIcon = toggle.querySelector('[data-eye-icon]');
                    const eyeSlashIcon = toggle.querySelector('[data-eye-slash-icon]');

                    if (!passwordInput || !eyeIcon || !eyeSlashIcon) return;

                    toggle.addEventListener('click', function() {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        eyeIcon.classList.toggle('hidden', type !== 'password');
                        eyeSlashIcon.classList.toggle('hidden', type === 'password');
                    });
                });
            });
        </script>
    </x-authentication-card>
</x-guest-layout>
