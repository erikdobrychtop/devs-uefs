<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Tag;
use App\Models\User;

class TagControllerTest extends TestCase
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
    public function it_can_list_all_tags()
    {
        Tag::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/tags');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_show_a_single_tag()
    {
        $tag = Tag::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/tags/{$tag->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Tag recuperada com sucesso.',
                'data' => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ],
            ]);
    }

    /** @test */
    public function it_can_create_a_tag()
    {
        $tagData = ['name' => $this->faker->word];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/tags', $tagData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Tag criada com sucesso.',
            ]);

        $this->assertDatabaseHas('tags', $tagData);
    }

    /** @test */
    public function it_can_update_a_tag()
    {
        $tag = Tag::factory()->create();

        $updateData = ['name' => 'Updated Tag'];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/tags/{$tag->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Tag atualizada com sucesso.',
            ]);

        $this->assertDatabaseHas('tags', $updateData);
    }

    /** @test */
    public function it_can_delete_a_tag()
    {
        $tag = Tag::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/tags/{$tag->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Tag removida com sucesso!',
            ]);

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }
}