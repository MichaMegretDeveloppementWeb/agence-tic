<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\Document;
use App\Models\Report;
use App\Models\SpecialPermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_director_g_returns_true_for_director_role(): void
    {
        $user = User::factory()->directorG()->create();

        $this->assertTrue($user->isDirectorG());
        $this->assertFalse($user->isAgent());
    }

    public function test_is_agent_returns_true_for_agent_role(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->isAgent());
        $this->assertFalse($user->isDirectorG());
    }

    public function test_can_access_director_g_always_has_access(): void
    {
        $director = User::factory()->directorG()->create();

        $this->assertTrue($director->canAccess(8));
        $this->assertTrue($director->canAccess(1));
        $this->assertTrue($director->canAccess(8, Report::class, 999));
    }

    public function test_can_access_agent_with_sufficient_level(): void
    {
        $agent = User::factory()->withLevel(5)->create();

        $this->assertTrue($agent->canAccess(5));
        $this->assertTrue($agent->canAccess(3));
        $this->assertTrue($agent->canAccess(1));
    }

    public function test_can_access_agent_with_insufficient_level(): void
    {
        $agent = User::factory()->withLevel(3)->create();

        $this->assertFalse($agent->canAccess(4));
        $this->assertFalse($agent->canAccess(8));
    }

    public function test_can_access_agent_with_special_permission_on_report(): void
    {
        $agent = User::factory()->withLevel(2)->create();
        $report = Report::factory()->withLevel(5)->create();

        SpecialPermission::factory()->create([
            'user_id' => $agent->id,
            'permissionable_type' => Report::class,
            'permissionable_id' => $report->id,
        ]);

        $this->assertTrue($agent->canAccess(5, Report::class, $report->id));
    }

    public function test_can_access_agent_with_special_permission_on_document(): void
    {
        $agent = User::factory()->withLevel(2)->create();
        $document = Document::factory()->withLevel(6)->create();

        SpecialPermission::factory()->create([
            'user_id' => $agent->id,
            'permissionable_type' => Document::class,
            'permissionable_id' => $document->id,
        ]);

        $this->assertTrue($agent->canAccess(6, Document::class, $document->id));
    }

    public function test_can_access_agent_without_special_permission_denied(): void
    {
        $agent = User::factory()->withLevel(2)->create();
        $report = Report::factory()->withLevel(5)->create();

        $this->assertFalse($agent->canAccess(5, Report::class, $report->id));
    }

    public function test_can_access_without_permissionable_info_falls_back_to_level(): void
    {
        $agent = User::factory()->withLevel(3)->create();

        $this->assertFalse($agent->canAccess(5));
        $this->assertFalse($agent->canAccess(5, null, null));
    }

    public function test_role_casting(): void
    {
        $agent = User::factory()->create();

        $this->assertInstanceOf(UserRole::class, $agent->role);
        $this->assertEquals(UserRole::Agent, $agent->role);
    }

    public function test_accreditation_level_casting(): void
    {
        $agent = User::factory()->withLevel(5)->create();

        $this->assertIsInt($agent->accreditation_level);
        $this->assertEquals(5, $agent->accreditation_level);
    }

    public function test_is_active_casting(): void
    {
        $active = User::factory()->create(['is_active' => true]);
        $inactive = User::factory()->inactive()->create();

        $this->assertTrue($active->is_active);
        $this->assertFalse($inactive->is_active);
    }
}
