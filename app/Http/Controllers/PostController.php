<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Posts",
 *     description="Endpoints relacionados a Posts"
 * )
 */
class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @OA\Get(
     *     path="/api/posts",
     *     tags={"Posts"},
     *     summary="Listar todos os posts",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $posts = $this->postService->getAllPosts();
            return response()->json([
                'message' => 'Lista de posts recuperada com sucesso.',
                'data' => $posts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao listar os posts.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Obter um post pelo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post recuperado com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado."
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $post = $this->postService->getPostById($id);

            if (!$post) {
                return response()->json([
                    'error' => 'Post não encontrado.',
                ], 404);
            }

            return response()->json([
                'message' => 'Post recuperado com sucesso.',
                'data' => $post,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar o post.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     tags={"Posts"},
     *     summary="Criar um novo post",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content", "user_id"},
     *             @OA\Property(property="title", type="string", example="Meu Primeiro Post"),
     *             @OA\Property(property="content", type="string", example="Conteúdo do post."),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="integer", example=2))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post criado com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação."
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'user_id' => 'required|exists:users,id',
                'tags' => 'sometimes|array',
                'tags.*' => 'integer|exists:tags,id',
            ]);

            $post = $this->postService->createPost($data);

            if (!empty($data['tags'])) {
                $post->tags()->sync($data['tags']);
            }

            return response()->json([
                'message' => 'Post criado com sucesso.',
                'data' => $post->load('tags'),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação.',
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar o post.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Atualizar um post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Título Atualizado"),
     *             @OA\Property(property="content", type="string", example="Conteúdo atualizado."),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="integer", example=3))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post atualizado com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado."
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'title' => 'sometimes|string|max:255',
                'content' => 'sometimes|string',
                'user_id' => 'sometimes|exists:users,id',
                'tags' => 'sometimes|array',
                'tags.*' => 'integer|exists:tags,id',
            ]);

            $post = $this->postService->updatePost($id, $data);

            if (!$post) {
                return response()->json([
                    'error' => 'Post não encontrado para atualização.',
                ], 404);
            }

            if (isset($data['tags'])) {
                $post->tags()->sync($data['tags']);
            }

            return response()->json([
                'message' => 'Post atualizado com sucesso.',
                'data' => $post->load('tags'),
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação.',
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar o post.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     tags={"Posts"},
     *     summary="Excluir um post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post removido com sucesso.",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Post removido com sucesso!"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado."
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->postService->deletePost($id);

            if (!$deleted) {
                return response()->json([
                    'error' => 'Post não encontrado para exclusão.',
                ], 404);
            }

            return response()->json([
                'message' => 'Post removido com sucesso!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao remover o post.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}