<?php

namespace Tests\Feature\Admin;

use App\Models\Report;
use App\Models\SpecialPermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PermissionManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_director_g_can_grant_permission(): void
    {
        $director = User::factory()->directorG()->create();
        $agent = User::factory()->withLevel(2)->create();
        $report = Report::factory()->withLevel(5)->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\PermissionManager::class)
            ->set('agentId', $agent->id)
            ->set('permissionableType', 'report')
            ->set('permissionableId', $report->id)
            ->call('grant')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $this->assertDatabaseHas('special_permissions', [
            'user_id' => $agent->id,
            'permissionable_type' => Report::class,
            'permissionable_id' => $report->id,
            'granted_by' => $director->id,
        ]);
    }

    public function test_cannot_grant_duplicate_permission(): void
    {
        $director = User::factory()->directorG()->create();
        $agent = User::factory()->create();
        $report = Report::factory()->create();

        SpecialPermission::factory()->create([
            'user_id' => $agent->id,
            'permissionable_type' => Report::class,
            'permissionable_id' => $report->id,
        ]);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\PermissionManager::class)
            ->set('agentId', $agent->id)
            ->set('permissionableType', 'report')
            ->set('permissionableId', $report->id)
            ->call('grant')
            ->assertHasErrors('permission-grant-failed');
    }

    public function test_grant_validation_requires_fields(): void
    {
        $director = User::factory()->directorG()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\PermissionManager::class)
            ->set('agentId', '')
            ->set('permissionableType', '')
            ->set('permissionableId', '')
            ->call('grant')
            ->assertHasErrors(['agentId', 'permissionableType', 'permissionableId']);
    }

    public function test_director_g_can_revoke_permission(): void
    {
        $director = User::factory()->directorG()->create();
        $permission = SpecialPermission::factory()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\PermissionManager::class)
            ->call('revoke', $permission->id)
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('special_permissions', ['id' => $permission->id]);
    }

    public function test_revoke_non_existent_permission_does_nothing(): void
    {
        $director = User::factory()->directorG()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\PermissionManager::class)
            ->call('revoke', 99999)
            ->assertHasNoErrors();
    }
}
