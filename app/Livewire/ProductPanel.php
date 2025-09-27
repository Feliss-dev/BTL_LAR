<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ProductPanel extends Component
{
    use WithPagination, WithoutUrlPagination;

    public const PAGINATE_COUNT = 9;

    public string $viewMode = 'grid';
    public $categoriesFilter = [];
    public $brandsFilter = [];
    public $priceRange = null;

    public string $sortProperty = 'id';
    public string $direction = 'desc';

    public function updating($property, $value) {
        Log::debug("Updating property: " . $property . ": " . $value);
    }

    public function updated($property) {
        if (str_starts_with($property, 'categoriesFilter')) {
            Log::debug("Current categories: [" . implode(', ', $this->categoriesFilter) . "]");
        }
    }

    public function render() {
        Log::debug("ProductPanel render.");
        $direction = $this->direction === 'asc' ? 'asc' : 'desc';

        $query = Product::query()->where('status', 'active');

        if ($this->categoriesFilter && count($this->categoriesFilter) > 0) {
            $query->whereIn('cat_id', $this->categoriesFilter);
        }

        if ($this->brandsFilter && count($this->brandsFilter) > 0) {
            $query->whereIn('brand_id', $this->brandsFilter);
        }

        // TODO: Size and Condition filter

        if ($this->priceRange) {
            $query->whereBetween('price', array_slice(explode('-', $this->priceRange), 0, 2));
        }

        switch ($this->sortProperty) {
            case 'id':
            default:
                $query->orderBy('id', $direction);
                break;

            case 'title':
                $query->orderBy('title', $direction);
                break;

            case 'price':
                $query->orderByRaw("price - price * (discount / 100) $direction");
                break;

            case 'stock':
                $query->orderBy('stock', $direction);
                break;
        }

        $maxPrice = Product::max('price');

        return view('livewire.product-panel', [
            'products' => $query->paginate(static::PAGINATE_COUNT),
            'maxPrice' => $maxPrice,
        ]);
    }
}
