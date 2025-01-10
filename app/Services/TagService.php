<?php

namespace App\Services;

use App\Repositories\TagRepository;

class TagService
{
    protected $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getAllTags()
    {
        return $this->tagRepository->all();
    }

    public function getTagById($id)
    {
        return $this->tagRepository->find($id);
    }

    public function createTag(array $data)
    {
        return $this->tagRepository->create($data);
    }

    public function updateTag($id, array $data)
    {
        $tag = $this->tagRepository->find($id);
        return $this->tagRepository->update($tag, $data);
    }

    public function deleteTag($id)
    {
        $tag = $this->tagRepository->find($id);
        return $this->tagRepository->delete($tag);
    }
}