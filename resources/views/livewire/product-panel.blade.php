<section class="product-area shop-sidebar shop section" x-init="
    $('#price_range').val('0-{{$maxPrice}}').trigger('change');

    $('#price_range').change(function (e) {
        $wire.$set('priceRange', e.target.value);
    });
">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-12">
                <div class="shop-sidebar">
                    <div class="single-widget category">
                        <h3 class="title">Danh mục</h3>

                        <ul>
                            @php
                                $parentCategories = App\Models\Category::getAllParentWithChild();
                            @endphp

                            @if ($parentCategories)
                                <li>
                                    @foreach($parentCategories as $parentCategory)
                                        <label class="d-flex flex-row align-items-center gap-1">
                                            <input type="checkbox" style="flex: none" value="{{ $parentCategory->slug }}" wire:model.live="categoriesFilter">

                                            {{$parentCategory->title}}
                                        </label>

                                        @if ($parentCategory->child_cat->count() > 0)
                                            <ul>
                                                @foreach ($parentCategory->child_cat as $childCategory)
                                                    <label class="d-flex flex-row align-items-center gap-3">
                                                        <input type="checkbox" style="flex: none" value="{{ $childCategory->slug }}" wire:model.live="categoriesFilter">

                                                        {{$childCategory->title}}
                                                    </label>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endforeach
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="single-widget range" wire:ignore>
                        <h3 class="title">Lọc theo giá cả</h3>
                        <div class="price-filter">
                            <div class="price-filter-inner">
                                <div id="slider-range" data-min="0" data-max="{{ $maxPrice }}"></div>

                                <div class="product_filter">
                                    <div class="label-input">
                                        <span>Range:</span>
                                        <input style="" type="text" id="amount" readonly/>
                                        <input type="hidden" name="price_range" id="price_range" value="0-{{$maxPrice}}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="single-widget category">
                        <h3 class="title">Hãng</h3>

                        <ul class="category-list">
                            @foreach(\App\Models\Brand::get() as $brand)
                                <label class="d-flex flex-row align-items-center gap-1">
                                    <input type="checkbox" style="flex: none" value="{{ $brand->slug }}" wire:model.live="brandsFilter">

                                    {{$brand->title}}
                                </label>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>

            <div class="col-lg-9 col-md-8 col-12" x-data="{
                init() {
                    $('#sortProperty').on('change', function (e) {
                        $wire.$set('sortProperty', e.target.value);
                    });

                    $('#sortDirection').on('change', function (e) {
                        $wire.$set('direction', e.target.value);
                    });

                    $('#gridViewMode').on('click', function() {
                        $wire.$set('viewMode', 'grid');
                    });

                    $('#listViewMode').on('click', function() {
                        $wire.$set('viewMode', 'list');
                    });
                }
            }">
                <div class="row">
                    <div class="col-12">
                        <!-- Shop Top -->
                        <div class="shop-top">
                            <div class="shop-shorter">
                                <div class="single-shorter d-flex flex-row flex-wrap gap-1">
                                    <div wire:ignore class="d-flex flex-row flex-wrap">
                                        <label for="sortProperty">
                                            Sắp xếp
                                        </label>

                                        <select id="sortProperty" style="width: 100% !important;">
                                            <option value="id">Mặc định</option>
                                            <option value="title" @if ($sortProperty == 'title') selected @endif>Theo tên</option>
                                            <option value="price" @if ($sortProperty == 'price') selected @endif>Theo giá cả</option>
                                            <option value="stock" @if ($sortProperty == 'stock') selected @endif>Theo tồn kho</option>
                                        </select>
                                    </div>

                                    <div wire:ignore>
                                        <label for="sortDirection">
                                            Hướng
                                        </label>

                                        <select id="sortDirection" style="width: 100% !important;">
                                            <option value="asc" @if ($direction == 'asc') selected @endif>Tăng dần</option>
                                            <option value="desc" @if ($direction == 'desc') selected @endif>Giảm dần</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="view-mode">
                                <button class="view-mode-button @if ($viewMode == 'grid') {{'view-mode-button-active'}} @endif" id="gridViewMode"><i class="fa fa-th-large"></i></button>
                                <button class="view-mode-button @if ($viewMode == 'list') {{'view-mode-button-active'}} @endif" id="listViewMode"><i class="fa fa-th-list"></i></button>
                            </div>
                        </div>
                        <!--/ End Shop Top -->
                    </div>
                </div>

                <div class="row">
                    @if(count($products)>0)
                        @foreach($products as $product)
                            @if ($viewMode == 'list')
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 col-sm-6">
                                            <div class="single-product">
                                                <livewire:product-image :product="$product"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-md-6 col-12">
                                            <div class="list-content">
                                                <div class="product-content">
                                                    <div class="product-price d-flex flex-row">
                                                        @if ($product->discount > 0)
                                                            <del class="ml-1">{{number_format($product->price, 0, ',', '.')}} đ</del>

                                                            @php
                                                                $after_discount = ($product->price-($product->price*$product->discount)/100);
                                                            @endphp

                                                            <p class="ml-1">{{number_format($product->price, 0, ',', '.')}} đ</p>
                                                        @else
                                                            <p>{{number_format($product->price, 0, ',', '.')}} đ</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <p class="des pt-2">{!! html_entity_decode($product->summary) !!}</p>
                                                <a href="javascript:void(0)" class="btn cart mt-2" data-id="{{$product->id}}">MUA NGAY!</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <x-product-card :key="$product->id" :product="$product" class="col-lg-4 col-md-6 col-12"/>
                            @endif
                        @endforeach
                    @else
                        <h4 class="text-warning" style="margin:100px auto;">There are no products.</h4>
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-12 justify-content-center d-flex">
                        {{$products->appends($_GET)->links('vendor.pagination.bootstrap-5')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
