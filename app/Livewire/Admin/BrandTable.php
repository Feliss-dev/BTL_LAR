<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use Livewire\Component;

class BrandTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = Brand::query();

        if (!empty($this->filter)) {
            $query->where('title', 'like', '%' . $this->filter . '%');
        }

        $brands = $query->paginate(10);

        return view('livewire.admin.brand-table', [
            'brands' => $brands,
        ]);
    }
}
