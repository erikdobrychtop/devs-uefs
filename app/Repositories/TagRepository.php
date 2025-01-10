<?php

namespace App\Repositories;

use App\Models\Tag;

class TagRepository
{
    public function all()
    {
        return Tag::all();
    }

    public function find($id)
    {
        return Tag::findOrFail($id);
    }

    public function create(array $data)
    {
        return Tag::create($data);
    }

    public function update(Tag $tag, array $data)
    {
        $tag->update($data);
        return $tag;
    }

    public function delete(Tag $tag)
    {
        return $tag->delete();
    }
}