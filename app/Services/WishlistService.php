<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Wishlist;
use App\User;

class WishlistService
{
    public function insert(Product $product, User $user) {
        $already_wishlist = Wishlist::where('user_id', auth()->user()->id)->where('cart_id',null)->where('product_id', $product->id)->first();

        if (!$already_wishlist) {
            $wishlist = new Wishlist;
            $wishlist->user_id = auth()->user()->id;
            $wishlist->product_id = $product->id;
            $wishlist->price = ($product->price - ($product->price * $product->discount) / 100);
            $wishlist->quantity = 1;
            $wishlist->amount = $wishlist->price*$wishlist->quantity;

            $wishlist->save();
        }
    }
}
