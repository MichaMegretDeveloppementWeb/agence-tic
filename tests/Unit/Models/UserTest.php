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

    public function testIsDirectorGReturnsTrueForDirectorRole(): void
    {
        $user = User::factory()->directorG()->create();

        $this->assertTrue($user->isDirectorG());
        $this->assertFalse($user->isAgent());
    }

    public function testIsAgentReturnsTrueForAgentRole(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->isAgent());
        $this->assertFalse($user->isDirectorG());
    }

    public function testCanAccessDirectorGAlwaysHasAccess(): void
    {
        $director = User::factory()->directorG()->create();

        $this->assertTrue($director->canAccess(8));
        $this->assertTrue($director->canAccess(1));
        $this->assertTrue($director->canAccess(8, Report::class, 999));
    }

    public function testCanAccessAgentWithSufficientLevel(): void
    {
        $agent = User::factory()->withLevel(5)->create();

        $this->assertTrue($agent->canAccess(5));
        $this->assertTrue($agent->canAccess(3));
        $this->assertTrue($agent->canAccess(1));
    }

    public function testCanAccessAgentWithInsufficientLevel(): void
    {
        $agent = User::factory()->withLevel(3)->create();

        $this->assertFalse($agent->canAccess(4));
        $this->assertFalse($agent->canAccess(8));
    }

    public function testCanAccessAgentWithSpecialPermissionOnReport(): void
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

    public function testCanAccessAgentWithSpecialPermissionOnDocument(): void
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

    public function testCanAccessAgentWithoutSpecialPermissionDenied(): void
    {
        $agent = User::factory()->withLevel(2)->create();
        $report = Report::factory()->withLevel(5)->create();

        $this->assertFalse($agent->canAccess(5, Report::class, $report->id));
    }

    public function testCanAccessWithoutPermissionableInfoFallsBackToLevel(): void
    {
        $agent = User::factory()->withLevel(3)->create();

        $this->assertFalse($agent->canAccess(5));
        $this->assertFalse($agent->canAccess(5, null, null));
    }

    public function testRoleCasting(): void
    {
        $agent = User::factory()->create();

        $this->assertInstanceOf(UserRole::class, $agent->role);
        $this->assertEquals(UserRole::Agent, $agent->role);
    }

    public function testAccreditationLevelCasting(): void
    {
        $agent = User::factory()->withLevel(5)->create();

        $this->assertIsInt($agent->accreditation_level);
        $this->assertEquals(5, $agent->accreditation_level);
    }

    public function testIsActiveCasting(): void
    {
        $active = User::factory()->create(['is_active' => true]);
        $inactive = User::factory()->inactive()->create();

        $this->assertTrue($active->is_active);
        $this->assertFalse($inactive->is_active);
    }
}
