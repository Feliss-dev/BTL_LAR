<div class="product-img" style="aspect-ratio: 1/1">
    <a href="{{ route('product-detail', $product->slug) }}" class="w-100 h-100" class="w-100 h-100">
        @php
            $photo = explode(',', $product->photo);
        @endphp

        <img class="default-img w-100 h-100" src="{{asset($photo[0])}}" alt="{{$photo[0]}}" style="object-fit: cover">

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
            <button data-toggle="modal" data-target="#{{$product->id}}" title="Quick View" href="#"><i class=" ti-eye"></i><span>Shop nhanh</span></button>
            <button type="button" wire:click="addToWishlist"><i class="ti-heart"></i><span>Thêm vào yêu thích</span></button>
        </div>

        <div class="product-action-2">
            <button type="button" wire:click="addToCart" class="border-0">Thêm vào giỏ hàng</button>
{{--            <a title="Add to cart" href="{{ route('add-to-cart', $product->slug) }}" wire:navigate>Thêm vào giỏ hàng</a>--}}
        </div>
    </div>
</div>
