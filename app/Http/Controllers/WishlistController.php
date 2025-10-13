<?php

namespace App\Http\Controllers;
use App\Services\WishlistService;
use Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
class WishlistController extends Controller
{
    public function __construct(private WishlistService $wishlistService) {
    }

    public function wishlist(Request $request){
        // dd($request->all());
        if (empty($request->slug)) {
            request()->session()->flash('error','Invalid Products');
            return back();
        }
        $product = Product::where('slug', $request->slug)->first();
        // return $product;
        if (empty($product)) {
            request()->session()->flash('error','Invalid Products');
            return back();
        }

        $this->wishlistService->insert($product, auth()->user());

        request()->session()->flash('success','Product successfully added to wishlist');
        return back();
    }

    public function unwishlist(Request $request) {
        $wishlist = Wishlist::find($request->id);

        if ($wishlist) {
            $wishlist->delete();
            request()->session()->flash('success','Wishlist successfully removed');
            return back();
        }

        request()->session()->flash('error','Error please try again');
        return back();
    }
}
