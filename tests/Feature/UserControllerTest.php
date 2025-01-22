<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = auth('api')->login($this->user);
    }

    /** @test */
    public function it_can_list_all_users()
    {
        User::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_show_a_single_user()
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/users/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Usuário recuperado com sucesso.',
                'data' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
            ]);
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $updateData = ['name' => 'Updated Name'];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/users/{$this->user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Usuário atualizado com sucesso.',
            ]);

        $this->assertDatabaseHas('users', $updateData);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/users/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Usuário removido com sucesso!',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
        ]);
    }
}