<div {{ $attributes }}>
    <div class="single-product">
        <x-product-image :product="$product"/>

        <div class="product-content">
            <h3><a href="{{route('product-detail', $product->slug)}}">{{$product->title}}</a></h3>
            <div class="product-price">
                @if ($product->discount > 0)
                    <del>{{number_format($product->price, 0, ',', '.')}} đ</del>

                    @php
                        $after_discount = ($product->price - ($product->price * $product->discount) / 100);
                    @endphp

                    <span class="ml-2">{{number_format($after_discount, 0, ',', '.')}} đ</span>
                @else
                    <p>{{number_format($product->price, 0, ',', '.')}} đ</p>
                @endif
            </div>
        </div>
    </div>
</div>
