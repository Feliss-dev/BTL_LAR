<?php

namespace App\Livewire\Admin;

use App\Models\PostTag;
use Livewire\Component;

class PostTagTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = PostTag::query();

        if (!empty($this->filter)) {
            $query->where('title', 'like', '%' . $this->filter . '%');
        }

        $tags = $query->paginate(10);

        return view('livewire.admin.post-tag-table', [
            'tags' => $tags,
        ]);
    }
}
