<div class="product-area section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-title">
                    <h2>Sản phẩm nổi bật</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="product-info" x-data="{ selectedCategoryId: -1 }">
                    <div class="nav-main">
                        <!-- Tab Nav -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @if ($categories)
                                <button class="btn" style="background:black" @click="selectedCategoryId = -1">
                                    Tất cả
                                </button>
                                @foreach ($categories as $key => $cat)
                                    <button class="btn" style="background:none;color:black;" @click="selectedCategoryId = {{ $cat->id }}">
                                        {{$cat->title}}
                                    </button>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div x-show="selectedCategoryId == -1" x-transition:enter.duration.500ms x-transition:leave.duration.500ms class="row row-cols-sm-6 row-cols-md-4 row-cols-lg-3">
                            @foreach ($featured_products_all_category as $product)
                                <x-product-card :product="$product" class="col-sm-6 col-md-4 col-lg-3 p-b-35"/>
                            @endforeach
                        </div>

                        @if ($categories)
                            @foreach ($categories as $category)
                                <div x-show="selectedCategoryId == {{ $category->id }}" x-transition:enter.duration.500ms x-transition:leave.duration.500ms class="row row-cols-sm-6 row-cols-md-4 row-cols-lg-3">
                                    @foreach ($featured_products[$category->id] as $product)
                                        <x-product-card :product="$product" class="col-sm-6 col-md-4 col-lg-3 p-b-35"/>
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
