<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Informations du profil') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Mettez à jour vos informations personnelles et votre adresse email.') }}
    </x-slot>

    <x-slot name="form">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6">
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Photo') }}" />

                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->full_name ?: $this->user->name }}" class="rounded-full size-20 object-cover">
                </div>

                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Sélectionner une nouvelle photo') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Supprimer la photo') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <div class="col-span-6 sm:col-span-3">
            <x-label for="nom" value="{{ __('Nom') }}" />
            <x-input id="nom" type="text" class="mt-1 block w-full input-field" wire:model="state.nom" required autocomplete="family-name" />
            <x-input-error for="nom" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="prénom" value="{{ __('Prénom') }}" />
            <x-input id="prénom" type="text" class="mt-1 block w-full input-field" wire:model="state.prénom" required autocomplete="given-name" />
            <x-input-error for="prénom" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full input-field" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2">
                    {{ __('Votre adresse email n\'est pas vérifiée.') }}

                    <button type="button" class="underline text-sm text-primary hover:text-primary-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" wire:click.prevent="sendEmailVerification">
                        {{ __('Cliquez ici pour renvoyer l\'email de vérification.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-accent-secondary">
                        {{ __('Un nouveau lien de vérification a été envoyé à votre adresse email.') }}
                    </p>
                @endif
            @endif
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="tél" value="{{ __('Téléphone') }}" />
            <x-input id="tél" type="tel" class="mt-1 block w-full input-field" wire:model="state.tél" autocomplete="tel" />
            <x-input-error for="tél" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="gouvernorat" value="{{ __('Gouvernorat') }}" />
            <x-input id="gouvernorat" type="text" class="mt-1 block w-full input-field" wire:model="state.gouvernorat" />
            <x-input-error for="gouvernorat" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="ville" value="{{ __('Ville') }}" />
            <x-input id="ville" type="text" class="mt-1 block w-full input-field" wire:model="state.ville" />
            <x-input-error for="ville" class="mt-2" />
        </div>

        <div class="col-span-6">
            <x-label for="adresse" value="{{ __('Adresse') }}" />
            <textarea id="adresse" rows="3" class="mt-1 block w-full input-field" wire:model="state.adresse"></textarea>
            <x-input-error for="adresse" class="mt-2" />
        </div>

    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Enregistré.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Enregistrer') }}
        </x-button>
    </x-slot>
</x-form-section>
