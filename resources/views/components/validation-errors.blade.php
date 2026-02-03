@if ($errors->any())
    @php
        $isLoginPage = request()->routeIs('login') || request()->is('login');
        $errorTitle = $isLoginPage ? 'Erreurs de connexion' : 'Erreurs de validation';
        $bgClass = $isLoginPage ? 'bg-white' : 'bg-danger/10';
        
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
    <div {{ $attributes->merge(['class' => "p-4 {$bgClass} border-l-4 border-danger rounded-lg"]) }} style="{{ $isLoginPage ? 'background-color: #fff !important;' : '' }}">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-danger mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-danger mb-2">
                    {{ __($errorTitle) }}
                </h3>
                <ul class="list-disc list-inside text-sm text-danger space-y-1">
                    @foreach ($isLoginPage && !empty($loginErrors) ? $loginErrors : $errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
