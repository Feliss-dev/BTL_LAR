<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;

class ProductCategoryTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = Category::query();

        if (!empty($this->filter)) {
            $query->where('title', 'like', '%' . $this->filter . '%');
        }

        $categories = $query->paginate(10);

        return view('livewire.admin.product-category-table', [
            'categories' => $categories,
        ]);
    }
}
