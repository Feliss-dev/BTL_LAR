<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Livewire\Component;

class CouponTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = Coupon::query();

        if (!empty($this->filter)) {
            $query->where('code', 'like', '%' . $this->filter . '%');
        }

        $coupons = $query->paginate(10);

        return view('livewire.admin.coupon-table', [
            'coupons' => $coupons,
        ]);
    }
}
