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

    public function test_any_user_can_view_category_show(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->get(route('categories.show', $category));

        $response->assertOk();
        $response->assertSee($category->name);
    }

    public function test_director_g_can_access_category_edit(): void
    {
        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($director)->get(route('categories.edit', $category));

        $response->assertOk();
    }

    public function test_agent_cannot_access_category_edit(): void
    {
        $agent = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($agent)->get(route('categories.edit', $category));

        $response->assertForbidden();
    }

    public function test_director_can_create_category(): void
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

    public function test_create_category_validates_required_fields(): void
    {
        $director = User::factory()->directorG()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors('name');
    }

    public function test_category_name_must_be_unique(): void
    {
        $director = User::factory()->directorG()->create();
        Category::factory()->create(['name' => 'Existante']);

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class)
            ->set('name', 'Existante')
            ->call('save')
            ->assertHasErrors('name');
    }

    public function test_director_can_update_category(): void
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

    public function test_name_uniqueness_ignores_current_category_on_update(): void
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

    public function test_can_delete_empty_category(): void
    {
        $director = User::factory()->directorG()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($director)
            ->test(\App\Livewire\Admin\CategoryForm::class, ['category' => $category])
            ->call('delete')
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_reports(): void
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

    public function test_cannot_delete_category_with_documents(): void
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
