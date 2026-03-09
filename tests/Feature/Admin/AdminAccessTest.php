<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string}> */
    public static function adminRoutesProvider(): array
    {
        return [
            'agents' => ['admin.agents.index'],
            'permissions' => ['admin.permissions.index'],
            'applications' => ['admin.applications.index'],
        ];
    }

    #[DataProvider('adminRoutesProvider')]
    public function test_director_g_can_access_admin_page(string $routeName): void
    {
        $director = User::factory()->directorG()->create();

        $response = $this->actingAs($director)->get(route($routeName));

        $response->assertOk();
    }

    #[DataProvider('adminRoutesProvider')]
    public function test_agent_cannot_access_admin_page(string $routeName): void
    {
        $agent = User::factory()->create();

        $response = $this->actingAs($agent)->get(route($routeName));

        $response->assertForbidden();
    }

    #[DataProvider('adminRoutesProvider')]
    public function test_guest_cannot_access_admin_page(string $routeName): void
    {
        $response = $this->get(route($routeName));

        $response->assertRedirect(route('login'));
    }

    /** @return array<string, array{string}> */
    public static function authenticatedRoutesProvider(): array
    {
        return [
            'reports' => ['reports.index'],
            'library' => ['library.index'],
            'categories' => ['categories.index'],
        ];
    }

    #[DataProvider('authenticatedRoutesProvider')]
    public function test_authenticated_user_can_access_page(string $routeName): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route($routeName));

        $response->assertOk();
    }

    #[DataProvider('authenticatedRoutesProvider')]
    public function test_guest_cannot_access_authenticated_page(string $routeName): void
    {
        $response = $this->get(route($routeName));

        $response->assertRedirect(route('login'));
    }

    /** @return array<string, array{string}> */
    public static function directorOnlyCreateRoutesProvider(): array
    {
        return [
            'categories.create' => ['categories.create'],
        ];
    }

    #[DataProvider('directorOnlyCreateRoutesProvider')]
    public function test_director_g_can_access_create_page(string $routeName): void
    {
        $director = User::factory()->directorG()->create();

        $response = $this->actingAs($director)->get(route($routeName));

        $response->assertOk();
    }

    #[DataProvider('directorOnlyCreateRoutesProvider')]
    public function test_agent_cannot_access_create_page(string $routeName): void
    {
        $agent = User::factory()->create();

        $response = $this->actingAs($agent)->get(route($routeName));

        $response->assertForbidden();
    }

    /** @return array<string, array{string}> */
    public static function authenticatedCreateRoutesProvider(): array
    {
        return [
            'reports.create' => ['reports.create'],
            'library.create' => ['library.create'],
        ];
    }

    #[DataProvider('authenticatedCreateRoutesProvider')]
    public function test_any_user_can_access_create_page(string $routeName): void
    {
        $agent = User::factory()->create();

        $response = $this->actingAs($agent)->get(route($routeName));

        $response->assertOk();
    }
}
