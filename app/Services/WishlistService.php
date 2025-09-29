<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Wishlist;
use App\User;

class WishlistService {
    public function insert(User $user, Product $product) {
        $already_wishlist = Wishlist::where('user_id', $user->id)->where('product_id', $product->id)->first();

        if (!$already_wishlist) {
            $wishlist = new Wishlist;
            $wishlist->user_id = $user->id;
            $wishlist->product_id = $product->id;
            $wishlist->price = ($product->price - ($product->price * $product->discount) / 100);
            $wishlist->quantity = 1;
            $wishlist->amount = $wishlist->price*$wishlist->quantity;

            $wishlist->save();
        }
    }

    public function remove(User $user, Product $product) {
        $wishlist = Wishlist::where('user_id', $user->id)->where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();
        }
    }

    public function isWishlisted(User $user, Product $product) {
        return Wishlist::where('user_id', $user->id)->where('product_id', $product->id)->first() != null;
    }
}
