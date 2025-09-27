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

    public string $displayType = 'grid';
    public ?array $categoriesFilter = null;
    public ?array $brandsFilter = null;
    public ?int $priceMin = null;
    public ?int $priceMax = null;

    public string $sortProperty = 'id';
    public string $direction = 'desc';

    public function updated($property) {
        Log::debug("Changed property: " . $property);
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

        if ($this->priceMin && $this->priceMax) {
            $query->whereBetween('price', [$this->priceMin, $this->priceMax]);
        } else if ($this->priceMin) {
            $query->where('price', '>=', $this->priceMin);
        } else if ($this->priceMax) {
            $query->where('price', '<=', $this->priceMax);
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

        return view('livewire.product-panel', [
            'products' => $query->paginate(static::PAGINATE_COUNT),
        ]);
    }
}
