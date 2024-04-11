<?php

namespace Tests\Feature\Api;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MessageApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sending a message to a chat.
     *
     * @return void
     */
    public function test_send_message()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $chat = Chat::create([
            'name' => 'Test Chat',
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
        ]);

        Sanctum::actingAs($user1);

        $response = $this->postJson("/api/v1/chats/messages/send", [
            'body' => 'Message text',
            'chat_id' => $chat->id,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test getting the list of messages.
     *
     * @return void
     */
    public function test_get_messages(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $chat = Chat::create([
            'name' => 'Test Chat 2',
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
        ]);

        Message::create([
            'body' => 'Message text 1',
            'chat_id' => $chat->id,
            'user_id' => $user1->id,
        ]);

        Message::create([
            'body' => 'Message text 2',
            'chat_id' => $chat->id,
            'user_id' => $user2->id,
        ]);

        Sanctum::actingAs($user1);

        $response = $this->getJson("/api/v1/chats/{$chat->id}/messages");

        $response->assertStatus(200);
    }
}
