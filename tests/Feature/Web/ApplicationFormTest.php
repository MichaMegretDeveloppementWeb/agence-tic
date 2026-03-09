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

    public function testRecruitmentPageIsAccessible(): void
    {
        $response = $this->get(route('recruitment'));

        $response->assertOk();
    }

    public function testVisitorCanSubmitApplication(): void
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

    public function testApplicationValidationRequiresFields(): void
    {
        Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', '')
            ->set('email', '')
            ->set('motivation', '')
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'motivation']);
    }

    public function testApplicationMotivationMinLength(): void
    {
        Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', 'Test')
            ->set('email', 'test@example.com')
            ->set('motivation', 'Trop court.')
            ->call('submit')
            ->assertHasErrors('motivation');
    }

    public function testApplicationEmailValidation(): void
    {
        Livewire::test(\App\Livewire\Web\ApplicationForm::class)
            ->set('name', 'Test')
            ->set('email', 'not-an-email')
            ->set('motivation', str_repeat('Motivation valide pour le test. ', 5))
            ->call('submit')
            ->assertHasErrors('email');
    }

    public function testApplicationIsRateLimited(): void
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

    public function testExperienceIsOptional(): void
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
