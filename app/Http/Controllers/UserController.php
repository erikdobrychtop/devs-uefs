<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return response()->json($this->userService->getAllUsers());
    }

    /**
     * Exibe um usuário específico.
     */
    public function show($id)
    {
        return response()->json($this->userService->getUserById($id));
    }

    /**
     * Cria um novo usuário.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data['password'] = bcrypt($data['password']);
        return response()->json($this->userService->createUser($data));
    }

    /**
     * Atualiza um usuário existente.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|min:6',
        ]);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        return response()->json($this->userService->updateUser($id, $data));
    }

    /**
     * Remove um usuário.
     */
    public function destroy($id)
    {
        $this->userService->deleteUser($id);

        return response()->json(['message' => 'Usuário removido com sucesso!'], 200);
    }
}