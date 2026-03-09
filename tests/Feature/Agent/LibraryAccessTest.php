<?php

namespace Tests\Feature\Agent;

use App\Models\Document;
use App\Models\SpecialPermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LibraryAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_view_library_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('library.index'));

        $response->assertOk();
    }

    public function test_agent_can_download_document_with_sufficient_level(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('documents/test.pdf', 'fake content');

        $user = User::factory()->withLevel(5)->create();
        $document = Document::factory()->withLevel(3)->create([
            'file_path' => 'documents/test.pdf',
            'file_name' => 'test.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('library.download', $document));

        $response->assertOk();
        $response->assertDownload('test.pdf');
    }

    public function test_agent_cannot_download_document_with_insufficient_level(): void
    {
        $user = User::factory()->withLevel(2)->create();
        $document = Document::factory()->withLevel(5)->create();

        $response = $this->actingAs($user)->get(route('library.download', $document));

        $response->assertForbidden();
    }

    public function test_agent_can_download_document_with_special_permission(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('documents/secret.pdf', 'classified');

        $user = User::factory()->withLevel(1)->create();
        $document = Document::factory()->withLevel(7)->create([
            'file_path' => 'documents/secret.pdf',
            'file_name' => 'secret.pdf',
        ]);

        SpecialPermission::factory()->create([
            'user_id' => $user->id,
            'permissionable_type' => Document::class,
            'permissionable_id' => $document->id,
        ]);

        $response = $this->actingAs($user)->get(route('library.download', $document));

        $response->assertOk();
        $response->assertDownload('secret.pdf');
    }

    public function test_download_returns404_when_file_is_missing(): void
    {
        Storage::fake('private');

        $user = User::factory()->withLevel(8)->create();
        $document = Document::factory()->withLevel(1)->create([
            'file_path' => 'documents/missing.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('library.download', $document));

        $response->assertNotFound();
    }

    public function test_director_g_can_download_any_document(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('documents/top-secret.pdf', 'content');

        $director = User::factory()->directorG()->create();
        $document = Document::factory()->withLevel(8)->create([
            'file_path' => 'documents/top-secret.pdf',
            'file_name' => 'top-secret.pdf',
        ]);

        $response = $this->actingAs($director)->get(route('library.download', $document));

        $response->assertOk();
        $response->assertDownload('top-secret.pdf');
    }

    public function test_agent_can_view_document_with_sufficient_level(): void
    {
        $user = User::factory()->withLevel(5)->create();
        $document = Document::factory()->withLevel(3)->create();

        $response = $this->actingAs($user)->get(route('library.show', $document));

        $response->assertOk();
        $response->assertSee($document->title);
    }

    public function test_agent_cannot_view_document_with_insufficient_level(): void
    {
        $user = User::factory()->withLevel(2)->create();
        $document = Document::factory()->withLevel(5)->create();

        $response = $this->actingAs($user)->get(route('library.show', $document));

        $response->assertForbidden();
    }

    public function test_agent_can_view_document_with_special_permission(): void
    {
        $user = User::factory()->withLevel(1)->create();
        $document = Document::factory()->withLevel(7)->create();

        SpecialPermission::factory()->create([
            'user_id' => $user->id,
            'permissionable_type' => Document::class,
            'permissionable_id' => $document->id,
        ]);

        $response = $this->actingAs($user)->get(route('library.show', $document));

        $response->assertOk();
        $response->assertSee($document->title);
    }

    public function test_director_g_can_view_any_document(): void
    {
        $director = User::factory()->directorG()->create();
        $document = Document::factory()->withLevel(8)->create();

        $response = $this->actingAs($director)->get(route('library.show', $document));

        $response->assertOk();
    }

    public function test_director_g_can_access_library_edit(): void
    {
        $director = User::factory()->directorG()->create();
        $document = Document::factory()->create();

        $response = $this->actingAs($director)->get(route('library.edit', $document));

        $response->assertOk();
    }

    public function test_agent_can_access_library_edit(): void
    {
        $agent = User::factory()->create();
        $document = Document::factory()->create();

        $response = $this->actingAs($agent)->get(route('library.edit', $document));

        $response->assertOk();
    }
}
