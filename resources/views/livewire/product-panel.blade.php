<section class="product-area shop-sidebar shop section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-12">
                <div class="shop-sidebar">
                    <x-category-panel/>

                    <x-price-range-filter title="Shop by Price" min="0" max="{{\App\Models\Product::max('price')}}"/>

                    <x-brands/>
                </div>
            </div>

            <div class="col-lg-9 col-md-8 col-12" x-data="{ sortProperty: $wire.entangle('sortProperty').live }">
                <div class="row">
                    <div class="col-12">
                        <!-- Shop Top -->
                        <div class="shop-top">
                            <div class="shop-shorter">
                                <div class="single-shorter">
                                    <label for="sortProperty">Sắp xếp</label>
                                    <select id="sortProperty" x-model="sortProperty">
                                        <option value="id">Mặc định</option>
                                        <option value="title" @if ($sortProperty == 'title') selected @endif>Theo tên</option>
                                        <option value="price" @if ($sortProperty == 'price') selected @endif>Theo giá cả</option>
                                        <option value="stock" @if ($sortProperty == 'stock') selected @endif>Theo tồn kho</option>
                                    </select>

                                    <label for="sortDirection">Hướng</label>
                                    <select id="sortDirection">
                                        <option value="asc" @if ($direction == 'asc') selected @endif>Tăng dần</option>
                                        <option value="desc" @if ($direction == 'desc') selected @endif>Giảm dần</option>
                                    </select>
                                </div>
                            </div>
                            <ul class="view-mode">
                                <li class="active"><a href="javascript:void(0)"><i class="fa fa-th-large"></i></a></li>
                                <li><a href="{{route('product-lists')}}"><i class="fa fa-th-list"></i></a></li>
                            </ul>
                        </div>
                        <!--/ End Shop Top -->
                    </div>
                </div>

                <div class="row">
                    @if(count($products)>0)
                        @foreach($products as $product)
                            <x-product-card :product="$product" class="col-lg-4 col-md-6 col-12"/>
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
