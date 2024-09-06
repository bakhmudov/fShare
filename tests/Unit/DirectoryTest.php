<?php

namespace Tests\Unit;

use App\Models\Directory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_directory()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/directories', [
            'name' => 'New Directory',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'user_id', 'created_at']);
    }

    public function test_user_can_delete_directory()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $directory = Directory::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/directories/{$directory->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Directory deleted']);
    }
}
