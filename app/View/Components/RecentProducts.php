<?php

namespace App\View\Components;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RecentProducts extends Component{
    public function __construct() {
    }

    public function render(): View|Closure|string {
        return view('components.recent-products', [
            'recent_products' => Product::where('status','active')->orderBy('id','DESC')->limit(3)->get(),
        ]);
    }
}
