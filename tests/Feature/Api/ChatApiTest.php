<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new chat.
     *
     * @return void
     */
    public function test_create_chat(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Sanctum::actingAs($user1);

        $response = $this->postJson('/api/v1/chats/create', [
            'name' => 'Test Chat',
            'user_id' => $user2->id,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test getting the list of chats.
     *
     * @return void
     */
    public function test_get_chats(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/chats/chats');

        $response->assertStatus(200);
    }
}
