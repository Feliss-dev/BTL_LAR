<?php

namespace App\Livewire\Admin;

use App\Models\ProductReview;
use Livewire\Component;

class ProductReviewTable extends Component
{
    public string $filter;

    public function render()
    {
        $query = ProductReview::query();

        if (!empty($this->filter)) {
            $query->join('users', 'users.id', '=', 'product_reviews.user_id')
                ->where('users.name', 'like', '%' . $this->filter . '%');
        }

        $reviews = $query->paginate(10);

        return view('livewire.admin.product-review-table', [
            'reviews' => $reviews,
        ]);
    }
}
