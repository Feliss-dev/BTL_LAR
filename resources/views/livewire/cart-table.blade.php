<table class="table shopping-summery">
    <thead>
        <tr class="main-hading">
            <th>SẢN PHẨM</th>
            <th>TÊN</th>
            <th class="text-center">GIÁ ĐƠN VỊ</th>
            <th class="text-center">SỐ LƯỢNG</th>
            <th class="text-center">TỔNG</th>
            <th class="text-center"><i class="ti-trash remove-icon"></i></th>
        </tr>
    </thead>

    <tbody id="cart_item_list">
        @php
            $carts = \App\Http\Helper::getAllProductFromCart();
        @endphp

        @if ($carts && $carts->count() > 0)
            @foreach ($carts as $cart)
                <livewire:cart-item-row :cart="$cart"/>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="6">Giỏ hàng trống...</td>
            </tr>
        @endif
    </tbody>
</table>
