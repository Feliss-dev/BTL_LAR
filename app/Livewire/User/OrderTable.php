<?php

namespace App\Livewire\User;

use App\Models\Order;
use Livewire\Component;

class OrderTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = Order::query()->where('user_id', auth()->id());

        if (!empty($this->filter)) {
            $query->where('order_number', 'like', '%' . $this->filter . '%');
        }

        $orders = $query->paginate(10);

        return view('livewire.user.order-table', [
            'orders' => $orders,
        ]);
    }
}
