<?php

namespace Tests\Feature\Agent;

use App\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    use RefreshDatabase;

    public function testAgentCanViewRemindersPage(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reminders.index'));

        $response->assertOk();
    }

    public function testAgentCanCreatePersonalReminder(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Agent\ReminderForm::class)
            ->set('title', 'Rappel test unitaire')
            ->set('content', 'Contenu du rappel')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('reminders', [
            'title' => 'Rappel test unitaire',
            'type' => ReminderType::Personal->value,
            'created_by' => $user->id,
        ]);
    }

    public function testReminderCreationValidation(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Agent\ReminderForm::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors('title');
    }

    public function testReminderTitleMinLength(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Agent\ReminderForm::class)
            ->set('title', 'ab')
            ->call('save')
            ->assertHasErrors('title');
    }

    public function testAgentCanToggleOwnReminder(): void
    {
        $user = User::factory()->create();
        $reminder = Reminder::factory()->create([
            'created_by' => $user->id,
            'type' => ReminderType::Personal,
            'is_completed' => false,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Agent\ReminderManager::class)
            ->call('toggleComplete', $reminder->id);

        $this->assertTrue($reminder->fresh()->is_completed);
    }

    public function testAgentCannotToggleOtherAgentReminder(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $reminder = Reminder::factory()->create([
            'created_by' => $other->id,
            'type' => ReminderType::Personal,
            'is_completed' => false,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Agent\ReminderManager::class)
            ->call('toggleComplete', $reminder->id);

        $this->assertFalse($reminder->fresh()->is_completed);
    }

    public function testAgentCanDeleteOwnReminder(): void
    {
        $user = User::factory()->create();
        $reminder = Reminder::factory()->create([
            'created_by' => $user->id,
            'type' => ReminderType::Personal,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Agent\ReminderManager::class)
            ->call('deleteReminder', $reminder->id)
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('reminders', ['id' => $reminder->id]);
    }

    public function testAgentCannotDeleteOtherAgentReminder(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $reminder = Reminder::factory()->create([
            'created_by' => $other->id,
            'type' => ReminderType::Personal,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Agent\ReminderManager::class)
            ->call('deleteReminder', $reminder->id);

        $this->assertDatabaseHas('reminders', ['id' => $reminder->id]);
    }

    public function testAgentCannotToggleGlobalReminder(): void
    {
        $user = User::factory()->create();
        $reminder = Reminder::factory()->global()->create([
            'created_by' => $user->id,
            'is_completed' => false,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Agent\ReminderManager::class)
            ->call('toggleComplete', $reminder->id);

        $this->assertFalse($reminder->fresh()->is_completed);
    }

    public function testAgentSeesGlobalReminders(): void
    {
        $user = User::factory()->create();
        $director = User::factory()->directorG()->create();

        Reminder::factory()->global()->create([
            'created_by' => $director->id,
            'title' => 'Annonce globale',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Agent\ReminderManager::class)
            ->assertSee('Annonce globale');
    }

    public function testAgentSeesTargetedReminders(): void
    {
        $user = User::factory()->create();
        $director = User::factory()->directorG()->create();

        Reminder::factory()->targeted($user)->create([
            'created_by' => $director->id,
            'title' => 'Rappel ciblé pour agent',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Agent\ReminderManager::class)
            ->assertSee('Rappel ciblé pour agent');
    }

    public function testDirectorGCanCreateGlobalReminder(): void
    {
        $director = User::factory()->directorG()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Agent\ReminderForm::class)
            ->set('title', 'Annonce globale test')
            ->set('content', 'Contenu global')
            ->set('type', 'global')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('reminders', [
            'title' => 'Annonce globale test',
            'type' => ReminderType::Global->value,
            'created_by' => $director->id,
        ]);
    }

    public function testDirectorGCanCreateTargetedReminder(): void
    {
        $director = User::factory()->directorG()->create();
        $agent = User::factory()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Agent\ReminderForm::class)
            ->set('title', 'Rappel ciblé test')
            ->set('type', 'targeted')
            ->set('targetUserId', (string) $agent->id)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('reminders', [
            'title' => 'Rappel ciblé test',
            'type' => ReminderType::Targeted->value,
            'target_user_id' => $agent->id,
            'created_by' => $director->id,
        ]);
    }

    public function testAgentCannotCreateGlobalReminder(): void
    {
        $agent = User::factory()->create();

        Livewire::actingAs($agent)
            ->test(\App\Livewire\Agent\ReminderForm::class)
            ->set('title', 'Tentative globale')
            ->set('type', 'global')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        // The type should be forced to personal
        $this->assertDatabaseHas('reminders', [
            'title' => 'Tentative globale',
            'type' => ReminderType::Personal->value,
            'created_by' => $agent->id,
        ]);
    }

    public function testTargetedReminderRequiresAgent(): void
    {
        $director = User::factory()->directorG()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Agent\ReminderForm::class)
            ->set('title', 'Rappel ciblé sans agent')
            ->set('type', 'targeted')
            ->set('targetUserId', '')
            ->call('save')
            ->assertHasErrors('targetUserId');
    }
}
