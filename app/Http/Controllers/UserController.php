<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Endpoints relacionados a Usuários"
 * )
 */
class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Listar todos os usuários",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $users = $this->userService->getAllUsers();
            return response()->json([
                'message' => 'Lista de usuários recuperada com sucesso.',
                'data' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao listar usuários.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Obter um usuário pelo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário recuperado com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado."
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $user = $this->userService->getUserById($id);

            if (!$user) {
                return response()->json([
                    'error' => 'Usuário não encontrado.',
                ], 404);
            }

            return response()->json([
                'message' => 'Usuário recuperado com sucesso.',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar o usuário.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Criar um novo usuário",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/User")
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
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:4|confirmed',
            ], [
                'password.confirmed' => 'A confirmação de senha não coincide com a senha.',
            ]);

            $data['password'] = Hash::make($data['password']);
            $user = $this->userService->createUser($data);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Usuário criado com sucesso.',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar usuário',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Atualizar um usuário",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso.",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado."
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:4|confirmed',
            ], [
                'password.confirmed' => 'A confirmação de senha não coincide com a senha.',
            ]);

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user = $this->userService->updateUser($id, $data);

            if (!$user) {
                return response()->json([
                    'error' => 'Usuário não encontrado.',
                ], 404);
            }

            return response()->json([
                'message' => 'Usuário atualizado com sucesso.',
                'data' => $user,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar usuário',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Remover um usuário",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário removido com sucesso.",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Usuário removido com sucesso!"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado."
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->userService->deleteUser($id);

            if (!$deleted) {
                return response()->json([
                    'error' => 'Usuário não encontrado.',
                ], 404);
            }

            return response()->json([
                'message' => 'Usuário removido com sucesso!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao remover usuário',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}