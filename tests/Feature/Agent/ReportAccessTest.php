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

    public function test_agent_can_view_report_list_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk();
    }

    public function test_agent_can_view_report_with_sufficient_level(): void
    {
        $user = User::factory()->withLevel(5)->create();
        $report = Report::factory()->withLevel(3)->create();

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertOk();
        $response->assertSee($report->code);
    }

    public function test_agent_cannot_view_report_with_insufficient_level(): void
    {
        $user = User::factory()->withLevel(2)->create();
        $report = Report::factory()->withLevel(5)->create();

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertForbidden();
    }

    public function test_agent_can_view_report_with_special_permission(): void
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

    public function test_director_g_can_view_any_report(): void
    {
        $director = User::factory()->directorG()->create();
        $report = Report::factory()->withLevel(8)->create();

        $response = $this->actingAs($director)->get(route('reports.show', $report));

        $response->assertOk();
    }

    public function test_report_show_loads_category_relation(): void
    {
        $user = User::factory()->withLevel(8)->create();
        $report = Report::factory()->withLevel(1)->create();

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertOk();
        $response->assertSee($report->category->name);
    }

    public function test_director_g_can_access_report_edit(): void
    {
        $director = User::factory()->directorG()->create();
        $report = Report::factory()->create();

        $response = $this->actingAs($director)->get(route('reports.edit', $report));

        $response->assertOk();
    }

    public function test_agent_can_access_report_edit(): void
    {
        $agent = User::factory()->create();
        $report = Report::factory()->create();

        $response = $this->actingAs($agent)->get(route('reports.edit', $report));

        $response->assertOk();
    }

    public function test_guest_cannot_view_report(): void
    {
        $report = Report::factory()->create();

        $response = $this->get(route('reports.show', $report));

        $response->assertRedirect(route('login'));
    }
}
