<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Wishlist;
use App\User;

class CartService {
    public function insert(Product $product, User $user) {
        $exist = Cart::where('user_id', $user->id)->where('order_id',null)->where('product_id', $product->id)->first();

        // return $already_cart;
        if($exist) {
            // dd($already_cart);
            $exist->quantity = $exist->quantity + 1;
            $exist->amount = $product->price+ $exist->amount;
            // return $already_cart->quantity;
            if ($exist->product->stock < $exist->quantity || $exist->product->stock <= 0) return back()->with('error','Stock not sufficient!.');

            $exist->save();
        } else {
            $cart = new Cart;
            $cart->user_id = $user->id;
            $cart->product_id = $product->id;
            $cart->price = ($product->price-($product->price*$product->discount)/100);
            $cart->quantity = 1;
            $cart->amount=$cart->price*$cart->quantity;

            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) return back()->with('error','Stock not sufficient!.');
            $cart->save();

            // Wishlist::where('user_id', auth()->user()->id)->where('cart_id', null)->update(['cart_id' => $cart->id]);
        }

        request()->session()->flash('success','Product successfully added to cart');
        return back();
    }
}
