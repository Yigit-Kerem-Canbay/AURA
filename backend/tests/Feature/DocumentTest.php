<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_authenticated_user_can_view_documents(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get('/documents');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_view_documents(): void
    {
        $response = $this->get('/documents');

        $response->assertRedirect('/login');
    }

    public function test_document_upload_requires_valid_file_type(): void
    {
        $user = \App\Models\User::factory()->create();

        \Illuminate\Support\Facades\Storage::fake('local');

        $file = \Illuminate\Http\Testing\File::create('document.exe', 100);

        $response = $this->actingAs($user)->post('/documents', [
            'document' => $file,
        ]);

        $response->assertSessionHasErrors('document');
    }
}
