<div {{ $attributes }}>
    <div class="single-product">
        <div class="product-img">
            <a href="{{route('product-detail', $product->slug)}}">
                @php
                    $photo = explode(',', $product->photo);
                @endphp

                <img class="default-img" src="{{$photo[0]}}" alt="{{$photo[0]}}">
                <img class="hover-img" src="{{$photo[0]}}" alt="{{$photo[0]}}">

                @if ($product->stock <= 0)
                    <span class="out-of-stock">Sale out</span>
                @elseif ($product->condition == 'new')
                    <span class="new">New</span
                @elseif ($product->condition == 'hot')
                    <span class="hot">Hot</span>
                @elseif ($product->discount > 0)
                    <span class="price-dec">{{$product->discount}}% Off</span>
                @endif
            </a>

            <div class="button-head">
                <div class="product-action">
                    <a data-toggle="modal" data-target="#{{$product->id}}" title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
                    <a title="Wishlist" href="{{route('add-to-wishlist',$product->slug)}}" ><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
                </div>
                <div class="product-action-2">
                    <a title="Add to cart" href="{{route('add-to-cart',$product->slug)}}">Add to cart</a>
                </div>
            </div>
        </div>

        <div class="product-content">
            <h3><a href="{{route('product-detail', $product->slug)}}">{{$product->title}}</a></h3>
            <div class="product-price">
                @if ($product->discount > 0)
                    <del style="padding-left:4%;">{{number_format($product->price, 0, ',', '.')}} đ</del>

                    @php
                        $after_discount = ($product->price - ($product->price * $product->discount) / 100);
                    @endphp

                    <span>{{number_format($after_discount, 0, ',', '.')}} đ</span>
                @else
                    <span>{{number_format($product->price, 0, ',', '.')}} đ</span>
                @endif
            </div>
        </div>
    </div>
</div>
