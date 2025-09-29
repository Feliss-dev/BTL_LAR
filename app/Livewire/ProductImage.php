<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use App\Services\WishlistService;
use Livewire\Component;

class ProductImage extends Component
{
    public Product $product;

    public function addToCart(CartService $cartService) {
        if (!auth()->check()) {
            return redirect()->route('login.form');
        }

        $cartService->insert($this->product, auth()->user());
        $this->dispatch('client-notification', message: 'Thêm vào giỏ hàng thành công.', type: 'success');
    }

    public function addToWishlist(WishlistService $wishlistService) {
        if (!auth()->check()) {
            return redirect()->route('login.form');
        }

        $wishlistService->insert($this->product, auth()->user());
        $this->dispatch('client-notification', message: 'Thêm vào danh sách yêu thích thành công.', type: 'success');
    }

    public function render()
    {
        return view('livewire.product-image');
    }
}
