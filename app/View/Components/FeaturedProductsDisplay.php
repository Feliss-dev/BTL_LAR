<?php

namespace App\View\Components;

use App\Models\Category;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FeaturedProductsDisplay extends Component
{
    public function __construct() {
    }

    public function render(): View|Closure|string {
        $categories = Category::where('status', 'active')->where('is_parent', 1)->get();
        $featuredProductsAllCategory = Product::where('status', 'active')->where('is_featured', 1)->orderBy('id', 'DESC')->limit(8)->get();

        // TODO: Optimize this query.
        $featuredProducts = [];

        foreach ($categories as $category) {
            $featuredProducts[$category->id] = Product::where('status', 'active')->where('is_featured', 1)->where('cat_id', $category->id)->orderBy('id', 'DESC')->limit(8)->get();
        }

        return view('components.featured-products-display', [
            'categories' => $categories,
            'featured_products_all_category' => $featuredProductsAllCategory,
            'featured_products' => $featuredProducts,
        ]);
    }
}
