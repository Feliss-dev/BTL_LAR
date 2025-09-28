<tr x-data="{ quantity: $wire.entangle('quantity').live }">
    @php
        $photo = explode(',', $cart->product['photo']);
    @endphp

    <td class="image" data-title="No">
        <img src="{{$photo[0]}}" alt="{{$photo[0]}}">
    </td>

    <td class="product-des" data-title="Description">
        <p class="product-name"><a href="{{route('product-detail',$cart->product->slug)}}" target="_blank">{{$cart->product->title}}</a></p>
        <p class="product-des">{!!($cart['summary']) !!}</p>
    </td>

    <td class="price" data-title="Price">
        {{number_format($cart->price, 0, ',', '.')}} đ
    </td>

    <td class="qty">
        <div class="input-group">
            <div class="button minus">
                <button type="button" class="btn btn-primary btn-number" disabled="disabled" @click="quantity--" :disabled="quantity <= 1">
                    <i class="ti-minus"></i>
                </button>
            </div>

            <input type="number" class="cart-quantity-input no-number-input-arrows" value="{{$cart->quantity}}" x-model="quantity" min="1">

            <div class="button plus">
                <button type="button" class="btn btn-primary btn-number" @click="quantity++">
                    <i class="ti-plus"></i>
                </button>
            </div>
        </div>
        <!--/ End Input Order -->
    </td>
    <td class="total-amount cart_single_price" data-title="Total"><span class="money">{{number_format($cart->amount, 0, ',', '.')}} đ</span></td>

    <td class="action" data-title="Remove"><a href="{{route('cart-delete',$cart->id)}}"><i class="ti-trash remove-icon"></i></a></td>
</tr>
