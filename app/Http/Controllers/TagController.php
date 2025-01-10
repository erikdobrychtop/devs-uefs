<?php

namespace App\Http\Controllers;

use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index()
    {
        return response()->json($this->tagService->getAllTags());
    }

    public function show($id)
    {
        return response()->json($this->tagService->getTagById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return response()->json($this->tagService->createTag($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        return response()->json($this->tagService->updateTag($id, $data));
    }

    public function destroy($id)
    {
        $this->tagService->deleteTag($id);

        return response()->json(['message' => 'Tag removida com sucesso!'], 200);
    }
}