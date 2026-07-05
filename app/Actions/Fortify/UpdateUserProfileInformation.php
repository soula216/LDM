<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'nom' => ['required', 'string', 'max:255'],
            'prénom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'tél' => ['nullable', 'string', 'max:20'],
            'gouvernorat' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        $profileData = $this->buildProfileData($input);

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $profileData);
        } else {
            $user->forceFill($profileData)->save();
        }
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    protected function buildProfileData(array $input): array
    {
        return [
            'nom' => $input['nom'],
            'prénom' => $input['prénom'],
            'name' => trim($input['nom'] . ' ' . $input['prénom']),
            'email' => $input['email'],
            'tél' => $input['tél'] ?? null,
            'gouvernorat' => $input['gouvernorat'] ?? null,
            'ville' => $input['ville'] ?? null,
            'adresse' => $input['adresse'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $profileData
     */
    protected function updateVerifiedUser(User $user, array $profileData): void
    {
        $user->forceFill(array_merge($profileData, [
            'email_verified_at' => null,
        ]))->save();

        $user->sendEmailVerificationNotification();
    }
}
