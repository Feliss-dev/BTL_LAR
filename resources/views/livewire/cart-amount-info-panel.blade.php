<ul>
    <li class="order_subtotal" data-price="{{\App\Http\Helper::totalCartPrice()}}">Thành tiền<span>{{number_format(\App\Http\Helper::totalCartPrice(), 0, ',', '.')}} đ</span></li>

    @if(session()->has('coupon'))
        <li class="coupon_price" data-price="{{Session::get('coupon')['value']}}">Tiết kiệm<span>{{number_format(Session::get('coupon')['value'], 0, ',', '.')}} đ</span></li>
    @endif

    @php
        $total_amount=\App\Http\Helper::totalCartPrice();

        if(session()->has('coupon')){
            $total_amount=$total_amount - \Illuminate\Support\Facades\Session::get('coupon')['value'];
        }
    @endphp

    <li class="last" id="order_total_price">Trả<span>{{number_format($total_amount, 0, ',', '.')}} đ</span></li>
</ul>
