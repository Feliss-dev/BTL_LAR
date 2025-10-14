<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Order @if($order)- {{$order->order_number}} @endif</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    @if ($order)
        <style>
            .invoice-header {
                background: #f7f7f7;
                padding: 10px 20px 10px 20px;
                border-bottom: 1px solid gray;
            }
            .site-logo {
                margin-top: 20px;
            }
            .invoice-right-top h3 {
                padding-right: 20px;
                margin-top: 20px;
                color: green;
                font-size: 30px!important;
                font-family: serif;
            }
            .invoice-left-top {
                border-left: 4px solid green;
                padding-left: 20px;
                padding-top: 20px;
            }
            .invoice-left-top p {
                margin: 0;
                line-height: 20px;
                font-size: 16px;
                margin-bottom: 3px;
            }
            thead {
                background: green;
                color: #FFF;
            }
            .authority h5 {
                margin-top: -10px;
                color: green;
            }
            .thanks h4 {
                color: green;
                font-size: 25px;
                font-weight: normal;
                font-family: serif;
                margin-top: 20px;
            }
            .site-address p {
                line-height: 6px;
                font-weight: 300;
            }
            .table tfoot .empty {
                border: none;
            }
            .table-bordered {
                border: none;
            }
            .table-header {
                padding: .75rem 1.25rem;
                margin-bottom: 0;
                background-color: rgba(0,0,0,.03);
                border-bottom: 1px solid rgba(0,0,0,.125);
            }
            .table td, .table th {
                padding: .30rem;
            }
        </style>
    @endif
</head>
<body>
    @if ($order)
        <div class="invoice-header">
            @php
                $settings = \App\Models\Setting::first();
            @endphp

            <div class="site-address">
                <h4>{{env('APP_NAME')}}</h4>
                <p>{{env('APP_ADDRESS')}}</p>
                <p>Phone: {{ $settings->phone }}</p>
                <p>Email: {{ $settings->email }}</p>
            </div>

            <div class="invoice-description">
                <div class="invoice-left-top float-left">
                    <h6>Invoice to</h6>
                    <h3>{{$order->name}}</h3>
                    <div class="address">
                        <p>
                            <strong>Country: </strong>
                            {{$order->country}}
                        </p>
                        <p>
                            <strong>Address: </strong>
                            {{ $order->address1 }} OR {{ $order->address2}}
                        </p>
                        <p><strong>Phone:</strong> {{ $order->phone }}</p>
                        <p><strong>Email:</strong> {{ $order->email }}</p>
                    </div>
                </div>

                <div class="invoice-right-top float-right" class="text-right">
                    <h3>Invoice #{{$order->order_number}}</h3>
                    <p>{{ $order->created_at->format('D d m Y') }}</p>
                    {{--                   <img class="img-responsive" src="data:image/png;base64, {{ base64_encode(::format('png')->size(150)->generate(route('admin.product.order.show', $order->id )))}}">--}}
                </div>

                <div class="clearfix"></div>
            </div>

            <section class="order_details pt-3">
                <div class="table-header">
                    <h5>Order Details</h5>
                </div>
                <table class="table table-bordered table-stripe">
                    <thead class="col-12">
                        <tr>
                            <th scope="col" class="col-6">Product</th>
                            <th scope="col" class="col-3">Quantity</th>
                            <th scope="col" class="col-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
{{--                        @foreach($order->cart_info as $cart)--}}
{{--                            <tr>--}}
{{--                                <td scope="col" class="col-6">$cart->product->title</td>--}}
{{--                                <td scope="col" class="col-3">{{$cart->quantity}}</td>--}}
{{--                                <td scope="col" class="col-3"><span>{{number_format($cart->price, 0, ',', '.')}} VND</span></td>--}}
{{--                            </tr>--}}
{{--                        @endforeach--}}

{{--                        <tr>--}}
{{--                            <th scope="col" class="empty"></th>--}}
{{--                            <th scope="col" class="text-right">Subtotal:</th>--}}
{{--                            <th scope="col"> <span>{{number_format($order->sub_total, 0, ',', '.')}} đ</span></th>--}}
{{--                        </tr>--}}
                    </tbody>
                    <tfoot>
                    @if(!empty($order->coupon))
                        <tr>
                            <th scope="col" class="empty"></th>
                            <th scope="col" class="text-right">Discount:</th>
                            <th scope="col"><span>-{{$order->coupon->discount(Helper::orderPrice($order->id, $order->user->id))}}{{Helper::base_currency()}}</span></th>
                        </tr>
                    @endif
{{--                    <tr>--}}
{{--                        <th scope="col" class="empty"></th>--}}
{{--                        @php--}}
{{--                            $shipping_charge = DB::table('shippings')->where('id',$order->shipping_id)->pluck('price');--}}
{{--                        @endphp--}}
{{--                        <th scope="col" class="text-right ">Shipping:</th>--}}
{{--                        <th><span>{{number_format($shipping_charge[0], 0, ',', '.')}} đ</span></th>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <th scope="col" class="empty"></th>--}}
{{--                        <th scope="col" class="text-right">Total:</th>--}}
{{--                        <th>--}}
{{--                        <span>--}}
{{--                            {{number_format($order->total_amount, 0, ',', '.')}} đ--}}
{{--                        </span>--}}
{{--                        </th>--}}
{{--                    </tr>--}}
                    </tfoot>
                </table>
            </section>

            <div class="clearfix"></div>
        </div>
    @else
        <h5 class="text-danger">Invalid</h5>
    @endif
</body>
</html>

{{--@php use Illuminate\Support\Facades\DB; @endphp--}}
{{--<!DOCTYPE html>--}}
{{--<html>--}}
{{--<head>--}}
{{--  <title>Order @if($order)- {{$order->order_number}} @endif</title>--}}
{{--  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">--}}
{{--</head>--}}
{{--<body>--}}

{{--@if($order)--}}
{{--  <section class="order_details pt-3">--}}
{{--    <div class="table-header">--}}
{{--      <h5>Order Details</h5>--}}
{{--    </div>--}}
{{--    <table class="table table-bordered table-stripe">--}}
{{--      <thead>--}}
{{--        <tr>--}}
{{--          <th scope="col" class="col-6">Product</th>--}}
{{--          <th scope="col" class="col-3">Quantity</th>--}}
{{--          <th scope="col" class="col-3">Total</th>--}}
{{--        </tr>--}}
{{--      </thead>--}}
{{--      <tbody>--}}
{{--      @foreach($order->cart_info as $cart)--}}
{{--      @php--}}
{{--        $product = DB::table('products')->select('title')->where('id',$cart->product_id)->get();--}}
{{--      @endphp--}}
{{--        <tr>--}}
{{--          <td><span>--}}
{{--              @foreach($product as $pro)--}}
{{--                {{$pro->title}}--}}
{{--              @endforeach--}}
{{--            </span></td>--}}
{{--          <td>x{{$cart->quantity}}</td>--}}
{{--          <td><span>{{number_format($cart->price, 0, ',', '.')}} đ</span></td>--}}
{{--        </tr>--}}
{{--      @endforeach--}}
{{--      </tbody>--}}
{{--      <tfoot>--}}
{{--        <tr>--}}
{{--          <th scope="col" class="empty"></th>--}}
{{--          <th scope="col" class="text-right">Subtotal:</th>--}}
{{--          <th scope="col"> <span>{{number_format($order->sub_total, 0, ',', '.')}} đ</span></th>--}}
{{--        </tr>--}}
{{--      --}}{{-- @if(!empty($order->coupon))--}}
{{--        <tr>--}}
{{--          <th scope="col" class="empty"></th>--}}
{{--          <th scope="col" class="text-right">Discount:</th>--}}
{{--          <th scope="col"><span>-{{$order->coupon->discount(Helper::orderPrice($order->id, $order->user->id))}}{{Helper::base_currency()}}</span></th>--}}
{{--        </tr>--}}
{{--      @endif --}}
{{--        <tr>--}}
{{--          <th scope="col" class="empty"></th>--}}
{{--          @php--}}
{{--            $shipping_charge = DB::table('shippings')->where('id',$order->shipping_id)->pluck('price');--}}
{{--          @endphp--}}
{{--          <th scope="col" class="text-right ">Shipping:</th>--}}
{{--          <th><span>{{number_format($shipping_charge[0], 0, ',', '.')}} đ</span></th>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--          <th scope="col" class="empty"></th>--}}
{{--          <th scope="col" class="text-right">Total:</th>--}}
{{--          <th>--}}
{{--            <span>--}}
{{--                {{number_format($order->total_amount, 0, ',', '.')}} đ--}}
{{--            </span>--}}
{{--          </th>--}}
{{--        </tr>--}}
{{--      </tfoot>--}}
{{--    </table>--}}
{{--  </section>--}}

{{--  <div class="clearfix"></div>--}}
{{--@else--}}
{{--  <h5 class="text-danger">Invalid</h5>--}}
{{--@endif--}}
{{--</body>--}}
{{--</html>--}}
