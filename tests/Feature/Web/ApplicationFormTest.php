<?php

namespace Tests\Feature\Web;

use App\Mail\NewApplicationNotification;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_recruitment_page_is_accessible(): void
    {
        $response = $this->get(route('recruitment'));

        $response->assertOk();
    }

    public function test_visitor_can_submit_application(): void
    {
        Mail::fake();

        Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', 'Agent Potentiel')
            ->set('email', 'test@example.com')
            ->set('motivation', str_repeat('Motivation de test pour rejoindre l\'agence. ', 5))
            ->set('experience', 'Expérience en surveillance.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('applications', [
            'name' => 'Agent Potentiel',
            'email' => 'test@example.com',
            'status' => 'pending',
        ]);

        Mail::assertQueued(NewApplicationNotification::class);
    }

    public function test_application_validation_requires_fields(): void
    {
        Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', '')
            ->set('email', '')
            ->set('motivation', '')
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'motivation']);
    }

    public function test_application_motivation_min_length(): void
    {
        Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', 'Test')
            ->set('email', 'test@example.com')
            ->set('motivation', 'Trop court.')
            ->call('submit')
            ->assertHasErrors('motivation');
    }

    public function test_application_email_validation(): void
    {
        Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', 'Test')
            ->set('email', 'not-an-email')
            ->set('motivation', str_repeat('Motivation valide pour le test. ', 5))
            ->call('submit')
            ->assertHasErrors('email');
    }

    public function test_application_is_rate_limited(): void
    {
        Mail::fake();

        $motivation = str_repeat('Motivation de test pour rejoindre l\'agence. ', 5);

        for ($i = 0; $i < 3; $i++) {
            Livewire::test(\App\Livewire\Web\ApplicationForm::class)
                ->set('name', "Agent $i")
                ->set('email', "agent{$i}@test.com")
                ->set('motivation', $motivation)
                ->call('submit')
                ->assertHasNoErrors();
        }

        Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', 'Agent 4')
            ->set('email', 'agent4@test.com')
            ->set('motivation', $motivation)
            ->call('submit')
            ->assertHasErrors('application-submit-throttled');

        $this->assertCount(3, Application::all());
    }

    public function test_experience_is_optional(): void
    {
        Mail::fake();

        Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', 'Agent')
            ->set('email', 'agent@test.com')
            ->set('motivation', str_repeat('Motivation de test suffisamment longue. ', 5))
            ->set('experience', '')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);
    }
}
