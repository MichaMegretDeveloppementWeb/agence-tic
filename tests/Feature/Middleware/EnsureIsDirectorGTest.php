<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureIsDirectorGTest extends TestCase
{
    use RefreshDatabase;

    public function testDirectorGCanAccessAdminRoutes(): void
    {
        $director = User::factory()->directorG()->create();

        $response = $this->actingAs($director)->get(route('admin.agents.index'));

        $response->assertOk();
    }

    public function testAgentCannotAccessAdminRoutes(): void
    {
        $agent = User::factory()->create();

        $response = $this->actingAs($agent)->get(route('admin.agents.index'));

        $response->assertForbidden();
    }

    public function testGuestCannotAccessAdminRoutes(): void
    {
        $response = $this->get(route('admin.agents.index'));

        $response->assertRedirect(route('login'));
    }
}
