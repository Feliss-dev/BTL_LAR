<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use function PHPUnit\Framework\matches;

class ProductPanel extends Component
{
    use WithPagination, WithoutUrlPagination;

    public const PAGINATE_COUNT = 9;

    public string $viewMode = 'grid';
    public $categoriesFilter = [];
    public $brandsFilter = [];
    public $search = [];
    public $priceRange = null;

    public string $sortProperty = 'id';
    public string $direction = 'desc';

    public function mount() {
        $this->viewMode = in_array(request()->get('view', 'grid'), ['grid', 'list']) ? request()->get('view', 'grid') : 'grid';
        $this->categoriesFilter = request()->has('categories') ? explode(',', request()->get('categories')) : [];
        $this->brandsFilter = request()->has('brands') ? explode(',', request()->get('brands')) : [];
        $this->sortProperty = request()->has('sortProperty') ? in_array(request()->get('sortProperty'), ['id', 'title', 'stock', 'price']) ? request()->get('sortProperty') : 'id' : 'id';
        $this->direction = in_array(request()->get('direction'), ['asc', 'desc']) ? request()->get('direction') : 'desc';
        $this->search = request()->get('search', '');

        // TODO: add price range params
    }

    public function render() {
        $direction = $this->direction === 'asc' ? 'asc' : 'desc';

        $query = Product::query()->where('status', 'active');

        if ($this->categoriesFilter && count($this->categoriesFilter) > 0) {
            $categoryIds = Category::whereIn('slug', $this->categoriesFilter)->pluck('id');

            if ($categoryIds->count() > 0) {
                $query->whereIn('cat_id', $categoryIds)->orWhereIn('child_cat_id', $categoryIds);
            }
        }

        if ($this->brandsFilter && count($this->brandsFilter) > 0) {
            $brandIds = Brand::whereIn('slug', $this->brandsFilter)->pluck('id');

            if ($brandIds->count() > 0) {
                $query->whereIn('brand_id', $brandIds);
            }
        }

        if ($this->search && strlen($this->search) > 0) {
            $query->where('title','like','%' . $this->search . '%')
                ->orwhere('slug','like','%' . $this->search . '%')
                ->orwhere('description','like','%' . $this->search . '%')
                ->orwhere('summary','like','%' . $this->search . '%')
                ->orwhere('price','like','%' . $this->search . '%');
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
