<?php

namespace Tests\Unit\Services;

use App\Models\ActivityEntry;
use App\Models\Report;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLoggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_log_creates_activity_entry(): void
    {
        $user = User::factory()->create();

        $entry = app(ActivityLogger::class)->log(
            'test_event',
            'Test message.',
            $user->id,
        );

        $this->assertInstanceOf(ActivityEntry::class, $entry);
        $this->assertDatabaseHas('activity_entries', [
            'event_type' => 'test_event',
            'message' => 'Test message.',
            'user_id' => $user->id,
        ]);
    }

    public function test_log_with_polymorphic_subject(): void
    {
        $user = User::factory()->create();
        $report = Report::factory()->create();

        $entry = app(ActivityLogger::class)->log(
            'report_viewed',
            'Rapport consulté.',
            $user->id,
            $report,
        );

        $this->assertEquals(Report::class, $entry->subject_type);
        $this->assertEquals($report->id, $entry->subject_id);
    }

    public function test_log_without_user(): void
    {
        $entry = app(ActivityLogger::class)->log(
            'system_event',
            'Événement système.',
        );

        $this->assertNull($entry->user_id);
        $this->assertDatabaseHas('activity_entries', [
            'event_type' => 'system_event',
            'user_id' => null,
        ]);
    }

    public function test_log_without_subject(): void
    {
        $user = User::factory()->create();

        $entry = app(ActivityLogger::class)->log(
            'login',
            'Connexion réussie.',
            $user->id,
        );

        $this->assertNull($entry->subject_type);
        $this->assertNull($entry->subject_id);
    }
}
