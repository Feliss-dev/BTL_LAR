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
                @if ($isWishlisted)
                    <button type="button" wire:click="removeFromWishlist">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>
                        </svg>

                        <span>Loại bỏ yêu thích</span>
                    </button>
                @else
                    <button type="button" wire:click="addToWishlist">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                        </svg>

                        <span>Thêm vào yêu thích</span>
                </button>
                @endif
        </div>

        <div class="product-action-2">
            <button type="button" wire:click="addToCart" class="border-0">Thêm vào giỏ hàng</button>
        </div>
    </div>
</div>
