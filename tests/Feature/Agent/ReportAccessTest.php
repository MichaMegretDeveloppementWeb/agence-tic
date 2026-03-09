<?php

namespace Tests\Feature\Agent;

use App\Models\Report;
use App\Models\SpecialPermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function testAgentCanViewReportListPage(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk();
    }

    public function testAgentCanViewReportWithSufficientLevel(): void
    {
        $user = User::factory()->withLevel(5)->create();
        $report = Report::factory()->withLevel(3)->create();

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertOk();
        $response->assertSee($report->code);
    }

    public function testAgentCannotViewReportWithInsufficientLevel(): void
    {
        $user = User::factory()->withLevel(2)->create();
        $report = Report::factory()->withLevel(5)->create();

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertForbidden();
    }

    public function testAgentCanViewReportWithSpecialPermission(): void
    {
        $user = User::factory()->withLevel(2)->create();
        $report = Report::factory()->withLevel(5)->create();

        SpecialPermission::factory()->create([
            'user_id' => $user->id,
            'permissionable_type' => Report::class,
            'permissionable_id' => $report->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertOk();
        $response->assertSee($report->code);
    }

    public function testDirectorGCanViewAnyReport(): void
    {
        $director = User::factory()->directorG()->create();
        $report = Report::factory()->withLevel(8)->create();

        $response = $this->actingAs($director)->get(route('reports.show', $report));

        $response->assertOk();
    }

    public function testReportShowLoadsCategoryRelation(): void
    {
        $user = User::factory()->withLevel(8)->create();
        $report = Report::factory()->withLevel(1)->create();

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertOk();
        $response->assertSee($report->category->name);
    }

    public function testDirectorGCanAccessReportEdit(): void
    {
        $director = User::factory()->directorG()->create();
        $report = Report::factory()->create();

        $response = $this->actingAs($director)->get(route('reports.edit', $report));

        $response->assertOk();
    }

    public function testAgentCanAccessReportEdit(): void
    {
        $agent = User::factory()->create();
        $report = Report::factory()->create();

        $response = $this->actingAs($agent)->get(route('reports.edit', $report));

        $response->assertOk();
    }

    public function testGuestCannotViewReport(): void
    {
        $report = Report::factory()->create();

        $response = $this->get(route('reports.show', $report));

        $response->assertRedirect(route('login'));
    }
}
