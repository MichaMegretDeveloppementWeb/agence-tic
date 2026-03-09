<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Document;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DocumentFormTest extends TestCase
{
    use RefreshDatabase;

    public function testDirectorCanCreateDocument(): void
    {
        Storage::fake('private');

        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\DocumentForm::class)
            ->set('title', 'Document de test')
            ->set('categoryId', (string) $category->id)
            ->set('accreditationLevel', 4)
            ->set('status', 'active')
            ->set('file', UploadedFile::fake()->create('rapport.pdf', 1024))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'title' => 'Document de test',
            'category_id' => $category->id,
            'accreditation_level' => 4,
            'uploaded_by' => $director->id,
        ]);
    }

    public function testCreateDocumentRequiresFile(): void
    {
        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\DocumentForm::class)
            ->set('title', 'Document sans fichier')
            ->set('categoryId', (string) $category->id)
            ->call('save')
            ->assertHasErrors('file');
    }

    public function testNotesAreSavedOnCreate(): void
    {
        Storage::fake('private');

        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\DocumentForm::class)
            ->set('title', 'Doc avec notes')
            ->set('categoryId', (string) $category->id)
            ->set('accreditationLevel', 1)
            ->set('status', 'active')
            ->set('notes', 'Notes de test importantes')
            ->set('file', UploadedFile::fake()->create('doc.pdf', 512))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'title' => 'Doc avec notes',
            'notes' => 'Notes de test importantes',
        ]);
    }

    public function testReportAssociationIsSaved(): void
    {
        Storage::fake('private');

        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();
        $report = Report::factory()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\DocumentForm::class)
            ->set('title', 'Doc lié à un rapport')
            ->set('categoryId', (string) $category->id)
            ->set('reportId', (string) $report->id)
            ->set('accreditationLevel', 1)
            ->set('status', 'active')
            ->set('file', UploadedFile::fake()->create('linked.pdf', 256))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'title' => 'Doc lié à un rapport',
            'report_id' => $report->id,
        ]);
    }

    public function testDirectorCanUpdateDocument(): void
    {
        Storage::fake('private');

        $director = User::factory()->directorG()->create();
        $document = Document::factory()->create(['notes' => 'Anciennes notes']);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\DocumentForm::class, ['document' => $document])
            ->assertSet('editMode', true)
            ->assertSet('notes', 'Anciennes notes')
            ->set('title', 'Titre modifié')
            ->set('notes', 'Nouvelles notes')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Titre modifié',
            'notes' => 'Nouvelles notes',
        ]);
    }

    public function testUpdateDocumentWithoutFileKeepsExistingFile(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('documents/existing.pdf', 'original content');

        $director = User::factory()->directorG()->create();
        $document = Document::factory()->create([
            'file_path' => 'documents/existing.pdf',
            'file_name' => 'existing.pdf',
        ]);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\DocumentForm::class, ['document' => $document])
            ->set('title', 'Titre mis à jour')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'file_path' => 'documents/existing.pdf',
            'file_name' => 'existing.pdf',
        ]);
    }

    public function testCreateDocumentValidatesRequiredFields(): void
    {
        $director = User::factory()->directorG()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\DocumentForm::class)
            ->set('title', '')
            ->set('categoryId', '')
            ->call('save')
            ->assertHasErrors(['title', 'categoryId', 'file']);
    }
}
