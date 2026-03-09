<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureAgentIsActiveTest extends TestCase
{
    use RefreshDatabase;

    public function testActiveAgentCanAccessProtectedRoutes(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
    }

    public function testInactiveAgentIsLoggedOutAndRedirected(): void
    {
        $user = User::factory()->inactive()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('toast-error');
        $this->assertGuest();
    }

    public function testGuestIsRedirectedToLogin(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}
