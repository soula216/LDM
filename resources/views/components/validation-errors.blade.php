@if ($errors->any())
    @php
        $isLoginPage = request()->routeIs('login') || request()->is('login');
        $isRegisterPage = request()->routeIs('register');
        $errorTitle = $isLoginPage ? 'Erreurs de connexion' : 'Erreurs de validation';
        $bgClass = $isLoginPage ? 'bg-white' : ($isRegisterPage ? 'auth-validation-errors' : 'bg-danger/10');
        
        // Pour la page de login, déterminer le type d'erreur
        $loginErrors = [];
        if ($isLoginPage) {
            $email = old('email');
            $hasEmailFormatError = false;
            $hasEmailRequiredError = false;
            
            // Fonction pour vérifier si l'email a un format valide
            $isValidEmailFormat = function($email) {
                return $email && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
            };
            
            // Vérifier d'abord les erreurs de validation pour email et password
            foreach ($errors->keys() as $key) {
                if ($key === 'email') {
                    // Vérifier le type d'erreur pour l'email
                    $emailErrors = $errors->get('email');
                    foreach ($emailErrors as $emailError) {
                        $errorLower = strtolower($emailError);
                        if (str_contains($errorLower, 'required') || str_contains($errorLower, 'obligatoire')) {
                            $loginErrors[] = 'Email obligatoire';
                            $hasEmailRequiredError = true;
                        } elseif (str_contains($errorLower, 'email') || str_contains($errorLower, 'format') || str_contains($errorLower, 'invalid')) {
                            $loginErrors[] = 'Format email invalid';
                            $hasEmailFormatError = true;
                        }
                    }
                } elseif ($key === 'password') {
                    // Vérifier le type d'erreur pour le password
                    $passwordErrors = $errors->get('password');
                    foreach ($passwordErrors as $passwordError) {
                        $errorLower = strtolower($passwordError);
                        if (str_contains($errorLower, 'required') || str_contains($errorLower, 'obligatoire')) {
                            $loginErrors[] = 'Mot de passe obligatoire';
                        }
                    }
                }
            }
            
            // Vérifier si l'email a un format invalide même si pas d'erreur de validation explicite
            if (!$hasEmailFormatError && !$hasEmailRequiredError && $email && !$isValidEmailFormat($email)) {
                $loginErrors[] = 'Format email invalid';
                $hasEmailFormatError = true;
            }
            
            // Si pas d'erreur de format ou required, vérifier les erreurs d'authentification
            // Ne vérifier l'existence dans la DB que si le format est valide
            if (!$hasEmailFormatError && !$hasEmailRequiredError) {
                foreach ($errors->keys() as $key) {
                    if ($key === 'password' && $errors->has('password')) {
                        $passwordErrors = $errors->get('password');
                        foreach ($passwordErrors as $passwordError) {
                            $errorLower = strtolower($passwordError);
                            if (!str_contains($errorLower, 'required') && !str_contains($errorLower, 'obligatoire')) {
                                // Erreur d'authentification pour le password
                                if ($email && $isValidEmailFormat($email) && \App\Models\User::where('email', $email)->exists()) {
                                    $loginErrors[] = 'Mot de passe incorrect';
                                } elseif ($email && $isValidEmailFormat($email)) {
                                    $loginErrors[] = 'Email incorrecte';
                                }
                            }
                        }
                    } elseif (str_contains(strtolower($key), 'auth')) {
                        // Erreur d'authentification générale
                        if ($email && $isValidEmailFormat($email) && \App\Models\User::where('email', $email)->exists()) {
                            $loginErrors[] = 'Mot de passe incorrect';
                        } elseif ($email && $isValidEmailFormat($email)) {
                            $loginErrors[] = 'Email incorrecte';
                        }
                    }
                }
                
                // Si aucune erreur spécifique n'a été trouvée, vérifier les messages d'erreur généraux
                if (empty($loginErrors)) {
                    foreach ($errors->all() as $error) {
                        $errorLower = strtolower($error);
                        // Vérifier si c'est une erreur d'authentification dans le message
                        if (str_contains($errorLower, 'auth.failed') || 
                            str_contains($errorLower, 'les identifiants') || 
                            str_contains($errorLower, 'incorrects') ||
                            str_contains($errorLower, 'credentials')) {
                            // Vérifier d'abord si le format de l'email est invalide
                            if ($email && !$isValidEmailFormat($email)) {
                                $loginErrors[] = 'Format email invalid';
                            } elseif ($email && $isValidEmailFormat($email) && \App\Models\User::where('email', $email)->exists()) {
                                $loginErrors[] = 'Mot de passe incorrect';
                            } elseif ($email && $isValidEmailFormat($email)) {
                                $loginErrors[] = 'Email incorrecte';
                            }
                        } else {
                            $loginErrors[] = $error;
                        }
                    }
                }
            }
        }
    @endphp
    <div {{ $attributes->merge(['class' => "p-4 {$bgClass} rounded-xl"]) }} style="{{ $isLoginPage ? 'background-color: #fff !important; border-left: 4px solid #f87171;' : '' }}">
        <div class="flex items-start gap-3">
            <svg @class([
                'w-5 h-5 flex-shrink-0 mt-0.5',
                'auth-validation-errors__icon' => $isRegisterPage,
                'text-red-500' => $isLoginPage,
                'text-danger' => ! $isLoginPage && ! $isRegisterPage,
            ]) fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1 min-w-0">
                <h3 @class([
                    'text-sm font-semibold mb-2',
                    'auth-validation-errors__title' => $isRegisterPage,
                    'text-red-600' => $isLoginPage,
                    'text-danger' => ! $isLoginPage && ! $isRegisterPage,
                ])>
                    {{ __($errorTitle) }}
                </h3>
                <ul @class([
                    'list-disc list-inside text-sm space-y-1',
                    'auth-validation-errors__list' => $isRegisterPage,
                    'text-red-600' => $isLoginPage,
                    'text-danger' => ! $isLoginPage && ! $isRegisterPage,
                ])>
                    @foreach ($isLoginPage && !empty($loginErrors) ? $loginErrors : $errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
