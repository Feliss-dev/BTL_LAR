<?php

namespace App\Livewire\Admin;

use App\Models\PostComment;
use Livewire\Component;

class PostCommentTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = PostComment::query();

        if (!empty($this->filter)) {
            $query->join('users', 'users.id', '=', 'post_comments.user_id')
                ->where('users.name', 'like', '%' . $this->filter . '%');
        }

        $comments = $query->paginate(10);

        return view('livewire.admin.post-comment-table', [
            'comments' => $comments,
        ]);
    }
}
