<?php

namespace App\Livewire;

use Livewire\Component;

class CartAmountInfoPanel extends Component
{
    protected $listeners = ['recalculate-cart-amount' => '$refresh'];

    public function render()
    {
        return view('livewire.cart-amount-info-panel');
    }
}
