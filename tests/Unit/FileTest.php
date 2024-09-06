<?php

namespace Tests\Unit;

use App\Models\Directory;
use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_files()
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $directory = Directory::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/files', [
            'files' => [UploadedFile::fake()->create('file1.txt', 100)],
            'directory_id' => $directory->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([['name', 'path', 'size', 'directory_id']]);

        Storage::disk('local')->assertExists('uploads/file1.txt');
    }

    public function test_user_can_toggle_file_public_status()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $file = File::factory()->create(['is_public' => false]);

        $response = $this->patchJson("/api/files/{$file->id}/toggle-public");

        $response->assertStatus(200)
            ->assertJson(['is_public' => true]);
    }
}
