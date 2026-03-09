<?php

namespace Tests\Feature\Agent;

use App\Models\Document;
use App\Models\Reminder;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function testDashboardPageLoadsSuccessfully(): void
    {
        $user = User::factory()->withLevel(3)->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Bienvenue');
        $response->assertSee($user->name);
    }

    public function testDashboardShowsCorrectReportCount(): void
    {
        $user = User::factory()->withLevel(3)->create();

        Report::factory()->withLevel(2)->count(3)->create();
        Report::factory()->withLevel(3)->count(2)->create();
        Report::factory()->withLevel(5)->count(4)->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('5'); // 3 level-2 + 2 level-3
    }

    public function testDashboardShowsCorrectDocumentCount(): void
    {
        $user = User::factory()->withLevel(4)->create();

        Document::factory()->withLevel(3)->count(2)->create(['status' => 'active']);
        Document::factory()->withLevel(5)->count(3)->create(['status' => 'active']);
        Document::factory()->withLevel(2)->archived()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('2'); // only 2 active level-3 docs
    }

    public function testDashboardShowsUserReminders(): void
    {
        $user = User::factory()->create();

        $reminder = Reminder::factory()->create([
            'created_by' => $user->id,
            'title' => 'Mon rappel test',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Mon rappel test');
    }
}
