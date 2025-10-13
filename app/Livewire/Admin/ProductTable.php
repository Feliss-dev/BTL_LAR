<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;

class ProductTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = Product::query();

        if (!empty($this->filter)) {
            $query->where('title', 'like', '%' . $this->filter . '%');
        }

        $products = $query->paginate(10);

        return view('livewire.admin.product-table', [
            'products' => $products,
        ]);
    }
}
