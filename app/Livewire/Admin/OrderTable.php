<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;

class OrderTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = Order::query();

        if (!empty($this->filter)) {
            $query->where('order_number', 'like', '%' . $this->filter . '%');
        }

        $orders = $query->paginate(10);

        return view('livewire.admin.order-table', [
            'orders' => $orders,
        ]);
    }
}
