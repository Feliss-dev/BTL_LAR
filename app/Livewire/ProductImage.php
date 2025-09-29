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
        $cartService->insert($this->product, auth()->user());
    }

    public function addToWishlist(WishlistService $wishlistService) {
        $wishlistService->insert($this->product, auth()->user());
    }

    public function render()
    {
        return view('livewire.product-image');
    }
}
