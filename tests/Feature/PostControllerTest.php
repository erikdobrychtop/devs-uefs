<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;

class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Criação de um usuário autenticado para os testes
        $this->user = User::factory()->create();

        // Geração de um token JWT para autenticação
        $this->token = auth('api')->login($this->user);
    }

    /** @test */
    public function it_can_list_all_posts()
    {
        Post::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id', 'title', 'content', 'user_id', 'tags', 'created_at', 'updated_at',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_show_a_single_post()
    {
        $post = Post::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post recuperado com sucesso.',
                'data' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                ],
            ]);
    }

    /** @test */
    public function it_can_create_a_post_with_tags()
    {
        $tags = Tag::factory()->count(3)->create();

        $postData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'user_id' => $this->user->id,
            'tags' => $tags->pluck('id')->toArray(),
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/posts', $postData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Post criado com sucesso.']);

        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
            'content' => $postData['content'],
            'user_id' => $this->user->id,
        ]);

        foreach ($tags as $tag) {
            $this->assertDatabaseHas('post_tag', [
                'post_id' => $response->json('data.id'),
                'tag_id' => $tag->id,
            ]);
        }
    }

    /** @test */
    public function it_can_update_a_post_and_its_tags()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $tags = Tag::factory()->count(2)->create();

        $updateData = [
            'title' => 'Título Atualizado',
            'content' => 'Conteúdo Atualizado',
            'tags' => $tags->pluck('id')->toArray(),
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post atualizado com sucesso.']);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Título Atualizado',
            'content' => 'Conteúdo Atualizado',
        ]);

        foreach ($tags as $tag) {
            $this->assertDatabaseHas('post_tag', [
                'post_id' => $post->id,
                'tag_id' => $tag->id,
            ]);
        }
    }

    /** @test */
    public function it_can_delete_a_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post removido com sucesso!']);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }
}