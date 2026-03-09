<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureIsDirectorGTest extends TestCase
{
    use RefreshDatabase;

    public function test_director_g_can_access_admin_routes(): void
    {
        $director = User::factory()->directorG()->create();

        $response = $this->actingAs($director)->get(route('admin.agents.index'));

        $response->assertOk();
    }

    public function test_agent_cannot_access_admin_routes(): void
    {
        $agent = User::factory()->create();

        $response = $this->actingAs($agent)->get(route('admin.agents.index'));

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get(route('admin.agents.index'));

        $response->assertRedirect(route('login'));
    }
}
