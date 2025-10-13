<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Livewire\Component;

class PostTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = Post::query();

        if (!empty($this->filter)) {
            $query->where('title', 'like', '%' . $this->filter . '%');
        }

        $posts = $query->paginate(10);

        return view('livewire.admin.post-table', [
            'posts' => $posts,
        ]);
    }
}
