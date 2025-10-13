<?php

namespace App\Livewire\Admin;

use App\Models\PostCategory;
use Livewire\Component;

class PostCategoryTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = PostCategory::query();

        if (!empty($this->filter)) {
            $query->where('title', 'like', '%' . $this->filter . '%');
        }

        $categories = $query->paginate(10);

        return view('livewire.admin.post-category-table', [
            'categories' => $categories,
        ]);
    }
}
