<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Livewire\Profile\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_profile_information_is_available(): void
    {
        $this->actingAs($user = User::factory()->create());

        $component = Livewire::test(UpdateProfileInformationForm::class);

        $this->assertEquals($user->nom, $component->state['nom']);
        $this->assertEquals($user->email, $component->state['email']);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->create([
            'nom' => 'Dupont',
            'prénom' => 'Jean',
        ]));

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', [
                'nom' => 'Martin',
                'prénom' => 'Paul',
                'email' => 'test@example.com',
                'tél' => null,
                'gouvernorat' => null,
                'ville' => null,
                'adresse' => null,
            ])
            ->call('updateProfileInformation');

        $user->refresh();

        $this->assertEquals('Martin', $user->nom);
        $this->assertEquals('Paul', $user->prénom);
        $this->assertEquals('Martin Paul', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }
}
