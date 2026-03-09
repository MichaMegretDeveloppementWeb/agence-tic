<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Document;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryFormTest extends TestCase
{
    use RefreshDatabase;

    public function testAnyUserCanViewCategoryShow(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->get(route('categories.show', $category));

        $response->assertOk();
        $response->assertSee($category->name);
    }

    public function testDirectorGCanAccessCategoryEdit(): void
    {
        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($director)->get(route('categories.edit', $category));

        $response->assertOk();
    }

    public function testAgentCannotAccessCategoryEdit(): void
    {
        $agent = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($agent)->get(route('categories.edit', $category));

        $response->assertForbidden();
    }

    public function testDirectorCanCreateCategory(): void
    {
        $director = User::factory()->directorG()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class)
            ->set('name', 'Nouvelle catégorie')
            ->set('description', 'Description de la catégorie')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('categories', [
            'name' => 'Nouvelle catégorie',
            'slug' => 'nouvelle-categorie',
            'description' => 'Description de la catégorie',
        ]);
    }

    public function testCreateCategoryValidatesRequiredFields(): void
    {
        $director = User::factory()->directorG()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors('name');
    }

    public function testCategoryNameMustBeUnique(): void
    {
        $director = User::factory()->directorG()->create();
        Category::factory()->create(['name' => 'Existante']);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class)
            ->set('name', 'Existante')
            ->call('save')
            ->assertHasErrors('name');
    }

    public function testDirectorCanUpdateCategory(): void
    {
        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create(['name' => 'Ancien nom']);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class, ['category' => $category])
            ->assertSet('editMode', true)
            ->assertSet('name', 'Ancien nom')
            ->set('name', 'Nouveau nom')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Nouveau nom',
            'slug' => 'nouveau-nom',
        ]);
    }

    public function testNameUniquenessIgnoresCurrentCategoryOnUpdate(): void
    {
        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create(['name' => 'Ma catégorie']);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class, ['category' => $category])
            ->set('description', 'Description mise à jour')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();
    }

    public function testCanDeleteEmptyCategory(): void
    {
        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class, ['category' => $category])
            ->call('delete')
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function testCannotDeleteCategoryWithReports(): void
    {
        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();
        Report::factory()->create(['category_id' => $category->id]);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class, ['category' => $category])
            ->call('delete')
            ->assertHasErrors('form-save-failed');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function testCannotDeleteCategoryWithDocuments(): void
    {
        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();
        Document::factory()->create(['category_id' => $category->id]);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class, ['category' => $category])
            ->call('delete')
            ->assertHasErrors('form-save-failed');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
