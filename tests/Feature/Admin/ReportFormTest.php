<?php

namespace Tests\Feature\Admin;

use App\Enums\ReportStatus;
use App\Enums\ThreatLevel;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportFormTest extends TestCase
{
    use RefreshDatabase;

    public function testDirectorCanCreateReport(): void
    {
        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\ReportForm::class)
            ->set('code', 'TIC-NEW-001')
            ->set('title', 'Nouveau rapport de test')
            ->set('categoryId', (string) $category->id)
            ->set('threatLevel', ThreatLevel::High->value)
            ->set('accreditationLevel', 3)
            ->set('description', 'Description du rapport de test avec au moins vingt caractères.')
            ->set('procedures', 'Procédure de confinement')
            ->set('notes', 'Notes complémentaires')
            ->set('status', ReportStatus::Active->value)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'code' => 'TIC-NEW-001',
            'title' => 'Nouveau rapport de test',
            'category_id' => $category->id,
            'threat_level' => ThreatLevel::High->value,
            'accreditation_level' => 3,
            'notes' => 'Notes complémentaires',
        ]);
    }

    public function testCreateReportValidatesRequiredFields(): void
    {
        $director = User::factory()->directorG()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\ReportForm::class)
            ->set('code', '')
            ->set('title', '')
            ->set('categoryId', '')
            ->set('description', '')
            ->call('save')
            ->assertHasErrors(['code', 'title', 'categoryId', 'description']);
    }

    public function testCodeMustBeUnique(): void
    {
        $director = User::factory()->directorG()->create();
        $existing = Report::factory()->create(['code' => 'TIC-EXIST']);
        $category = Category::factory()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\ReportForm::class)
            ->set('code', 'TIC-EXIST')
            ->set('title', 'Un titre valide')
            ->set('categoryId', (string) $category->id)
            ->set('description', 'Description valide avec plus de vingt caractères ici.')
            ->set('status', ReportStatus::Active->value)
            ->call('save')
            ->assertHasErrors('code');
    }

    public function testDirectorCanUpdateReport(): void
    {
        $director = User::factory()->directorG()->create();
        $report = Report::factory()->create(['title' => 'Ancien titre']);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\ReportForm::class, ['report' => $report])
            ->assertSet('editMode', true)
            ->assertSet('title', 'Ancien titre')
            ->set('title', 'Nouveau titre')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'title' => 'Nouveau titre',
        ]);
    }

    public function testCodeUniquenessIgnoresCurrentReportOnUpdate(): void
    {
        $director = User::factory()->directorG()->create();
        $report = Report::factory()->create(['code' => 'TIC-MINE']);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\ReportForm::class, ['report' => $report])
            ->set('title', 'Titre mis à jour')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();
    }
}
