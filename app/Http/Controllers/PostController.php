<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index()
    {
        return response()->json($this->postService->getAllPosts());
    }

    public function show($id)
    {
        return response()->json($this->postService->getPostById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        return response()->json($this->postService->createPost($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        return response()->json($this->postService->updatePost($id, $data));
    }

    public function destroy($id)
    {
        $this->postService->deletePost($id);

        return response()->json(['message' => 'Post removido com sucesso!'], 200);
    }
}