<?php

namespace App\Livewire\User;

use App\Models\ProductReview;
use Livewire\Component;

class ProductReviewTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = ProductReview::query()->where('user_id', auth()->id());

        if (!empty($this->filter)) {
            $query->join('products', 'products.id', '=', 'product_reviews.product_id')
                ->where('products.title', 'like', '%' . $this->filter . '%');
        }

        $reviews = $query->paginate(10);

        return view('livewire.user.product-review-table', [
            'reviews' => $reviews,
        ]);
    }
}
