<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration endpoint.
     *
     * @return void
     */
    public function test_user_registration()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test getting access token after user login.
     *
     * @return void
     */
    public function test_get_access_token()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test getting the list of users.
     *
     * @return void
     */
    public function test_get_users_list()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        User::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200);
    }
}
