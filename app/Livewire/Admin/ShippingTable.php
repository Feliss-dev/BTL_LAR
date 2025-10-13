<?php

namespace App\Livewire\Admin;

use App\Models\Shipping;
use Livewire\Component;

class ShippingTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = Shipping::query();

        if (!empty($this->filter)) {
            $query->where('type', 'like', '%' . $this->filter . '%');
        }

        $shippings = $query->paginate(10);

        return view('livewire.admin.shipping-table', [
            'shippings' => $shippings,
        ]);
    }
}
