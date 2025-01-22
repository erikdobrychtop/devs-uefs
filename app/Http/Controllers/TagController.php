<?php

namespace App\Http\Controllers;

use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Tags",
 *     description="Endpoints relacionados a Tags"
 * )
 */
class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * @OA\Get(
     *     path="/api/tags",
     *     tags={"Tags"},
     *     summary="Listar todas as tags",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tags",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Tag")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $tags = $this->tagService->getAllTags();
            return response()->json([
                'message' => 'Lista de tags recuperada com sucesso.',
                'data' => $tags,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao listar as tags.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/tags/{id}",
     *     tags={"Tags"},
     *     summary="Obter uma tag pelo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tag",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag recuperada com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tag não encontrada."
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $tag = $this->tagService->getTagById($id);

            if (!$tag) {
                return response()->json([
                    'error' => 'Tag não encontrada.',
                ], 404);
            }

            return response()->json([
                'message' => 'Tag recuperada com sucesso.',
                'data' => $tag,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar a tag.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/tags",
     *     tags={"Tags"},
     *     summary="Criar uma nova tag",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Laravel")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tag criada com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
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
                'name' => 'required|string|max:255',
            ]);

            $tag = $this->tagService->createTag($data);

            return response()->json([
                'message' => 'Tag criada com sucesso.',
                'data' => $tag,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação.',
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar a tag.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/tags/{id}",
     *     tags={"Tags"},
     *     summary="Atualizar uma tag",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tag",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Tag Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag atualizada com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tag não encontrada."
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
            ]);

            $tag = $this->tagService->updateTag($id, $data);

            if (!$tag) {
                return response()->json([
                    'error' => 'Tag não encontrada para atualização.',
                ], 404);
            }

            return response()->json([
                'message' => 'Tag atualizada com sucesso.',
                'data' => $tag,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação.',
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar a tag.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/tags/{id}",
     *     tags={"Tags"},
     *     summary="Remover uma tag",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tag",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag removida com sucesso.",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Tag removida com sucesso!"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tag não encontrada."
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->tagService->deleteTag($id);

            if (!$deleted) {
                return response()->json([
                    'error' => 'Tag não encontrada para exclusão.',
                ], 404);
            }

            return response()->json([
                'message' => 'Tag removida com sucesso!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao remover a tag.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}