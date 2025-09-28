<?php

namespace App\Livewire;

use App\Models\Cart;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CartItemRow extends Component
{
    public Cart $cart;
    public $quantity;

    public function mount(Cart $cart) {
        $this->cart = $cart;
        $this->quantity = $cart->quantity;
    }

    public function updatedQuantity() {
        if ($this->quantity == null) {
            $this->quantity = 1;
        }

        $this->quantity = max(1, $this->quantity);
        $this->cart->quantity = $this->quantity;
        $this->cart->amount = $this->quantity * $this->cart->price;
        $this->cart->save();

        $this->dispatch('recalculate-cart-amount');
    }

    public function render()
    {
        return view('livewire.cart-item-row');
    }
}
