<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Support\Facades\Log;
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

        $wishlistService->insert(auth()->user(), $this->product);
        $this->dispatch('client-notification', message: 'Thêm vào danh sách yêu thích thành công.', type: 'success');
    }

    public function removeFromWishlist(WishlistService $wishlistService) {
        if (!auth()->check()) return;

        $wishlistService->remove(auth()->user(), $this->product);
        $this->dispatch('client-notification', message: 'Loại khỏi danh sách yêu thích thành công.', type: 'success');
    }

    public function render(WishlistService $wishlistService) {
        $wishlisted = $wishlistService->isWishlisted(auth()->user(), $this->product);

        return view('livewire.product-image', [
            'isWishlisted' => $wishlisted,
        ]);
    }
}
