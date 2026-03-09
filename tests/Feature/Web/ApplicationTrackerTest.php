<?php

namespace Tests\Feature\Web;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicationTrackerTest extends TestCase
{
    use RefreshDatabase;

    public function testTrackingPageIsAccessible(): void
    {
        $response = $this->get(route('recruitment.tracking'));

        $response->assertOk();
    }

    public function testCanSearchApplicationByTrackingCode(): void
    {
        $application = Application::factory()->create([
            'tracking_code' => 'TIC-A3B7K9M2',
            'status' => ApplicationStatus::Pending,
        ]);

        Livewire::test(\App\Livewire\Web\ApplicationTracker::class)
            ->set('trackingCode', 'TIC-A3B7K9M2')
            ->call('search')
            ->assertHasNoErrors()
            ->assertSet('searched', true)
            ->assertSet('application.id', $application->id);
    }

    public function testSearchIsCaseInsensitive(): void
    {
        $application = Application::factory()->create([
            'tracking_code' => 'TIC-A3B7K9M2',
            'status' => ApplicationStatus::Pending,
        ]);

        Livewire::test(\App\Livewire\Web\ApplicationTracker::class)
            ->set('trackingCode', 'tic-a3b7k9m2')
            ->call('search')
            ->assertHasNoErrors()
            ->assertSet('searched', true)
            ->assertSet('application.id', $application->id);
    }

    public function testShowsErrorWhenApplicationNotFound(): void
    {
        Livewire::test(\App\Livewire\Web\ApplicationTracker::class)
            ->set('trackingCode', 'TIC-ZZZZZZZZ')
            ->call('search')
            ->assertHasErrors('tracking-not-found')
            ->assertSet('searched', true)
            ->assertSet('application', null);
    }

    public function testTrackingCodeIsRequired(): void
    {
        Livewire::test(\App\Livewire\Web\ApplicationTracker::class)
            ->set('trackingCode', '')
            ->call('search')
            ->assertHasErrors('trackingCode');
    }

    public function testTrackingCodeMustBeExactly12Characters(): void
    {
        Livewire::test(\App\Livewire\Web\ApplicationTracker::class)
            ->set('trackingCode', 'TIC-SHORT')
            ->call('search')
            ->assertHasErrors('trackingCode');
    }

    public function testDisplaysApplicationStatusBadge(): void
    {
        Application::factory()->create([
            'tracking_code' => 'TIC-TEST1234',
            'name' => 'Agent Test',
            'email' => 'agent@test.com',
            'status' => ApplicationStatus::Accepted,
        ]);

        Livewire::test(\App\Livewire\Web\ApplicationTracker::class)
            ->set('trackingCode', 'TIC-TEST1234')
            ->call('search')
            ->assertHasNoErrors()
            ->assertSee('Agent Test')
            ->assertSee('agent@test.com')
            ->assertSee('Acceptée');
    }

    public function testTrackingCodeIsGeneratedOnSubmission(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', 'Agent Suivi')
            ->set('email', 'suivi@test.com')
            ->set('motivation', str_repeat('Motivation de test pour le suivi de candidature. ', 5))
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $application = Application::where('email', 'suivi@test.com')->first();

        $this->assertNotNull($application->tracking_code);
        $this->assertMatchesRegularExpression('/^TIC-[A-Z0-9]{8}$/', $application->tracking_code);
    }

    public function testTrackingCodeIsDisplayedAfterSubmission(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $component = Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', 'Agent Display')
            ->set('email', 'display@test.com')
            ->set('motivation', str_repeat('Motivation de test pour affichage du code. ', 5))
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $trackingCode = $component->get('trackingCode');

        $this->assertNotEmpty($trackingCode);
        $this->assertMatchesRegularExpression('/^TIC-[A-Z0-9]{8}$/', $trackingCode);

        $component->assertSee($trackingCode);
    }
}
