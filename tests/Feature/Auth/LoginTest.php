<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginPageIsAccessible(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Identifiez-vous');
    }

    public function testAgentCanLoginWithValidCredentials(): void
    {
        $user = User::factory()->create([
            'agent_code' => 'AG-001',
            'password' => 'password',
        ]);

        Livewire::test(\App\Livewire\Auth\LoginForm::class)
            ->set('agent_code', 'AG-001')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function testAgentCannotLoginWithWrongPassword(): void
    {
        User::factory()->create([
            'agent_code' => 'AG-001',
            'password' => 'password',
        ]);

        Livewire::test(\App\Livewire\Auth\LoginForm::class)
            ->set('agent_code', 'AG-001')
            ->set('password', 'wrong-password')
            ->call('authenticate')
            ->assertHasErrors('login-failed')
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function testInactiveAgentCannotLogin(): void
    {
        User::factory()->inactive()->create([
            'agent_code' => 'AG-002',
            'password' => 'password',
        ]);

        Livewire::test(\App\Livewire\Auth\LoginForm::class)
            ->set('agent_code', 'AG-002')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasErrors('login-failed')
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function testValidationRequiresAgentCodeAndPassword(): void
    {
        Livewire::test(\App\Livewire\Auth\LoginForm::class)
            ->set('agent_code', '')
            ->set('password', '')
            ->call('authenticate')
            ->assertHasErrors(['agent_code', 'password']);
    }

    public function testLoginIsRateLimited(): void
    {
        User::factory()->create([
            'agent_code' => 'AG-003',
            'password' => 'password',
        ]);

        $component = Livewire::test(\App\Livewire\Auth\LoginForm::class);

        for ($i = 0; $i < 5; $i++) {
            $component
                ->set('agent_code', 'AG-003')
                ->set('password', 'wrong')
                ->call('authenticate');
        }

        $component
            ->set('agent_code', 'AG-003')
            ->set('password', 'wrong')
            ->call('authenticate')
            ->assertHasErrors('login-throttled');
    }

    public function testLogoutRedirectsToHome(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
