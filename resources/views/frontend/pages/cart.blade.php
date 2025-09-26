@extends('frontend.layouts.master')
@section('title','Giỏ hàng')
@section('main-content')
    @php
        $breadcrumbPath = [
            new \App\View\Components\BreadcrumbElement('Trang chủ', route('home')),
            new \App\View\Components\BreadcrumbElement('Giỏ hàng', null),
        ];
    @endphp

    <x-breadcrumb :elements="$breadcrumbPath"/>

	<!-- Shopping Cart -->
	<div class="shopping-cart section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<!-- Shopping Summery -->
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
							<form action="{{route('cart.update')}}" method="POST">
								@csrf
								@if(\App\Http\Helper::getAllProductFromCart())
									@foreach(\App\Http\Helper::getAllProductFromCart() as $key=>$cart)
										<tr>
											@php
											    $photo = explode(',',$cart->product['photo']);
											@endphp

											<td class="image" data-title="No">
                                                <img src="{{$photo[0]}}" alt="{{$photo[0]}}">
                                            </td>

											<td class="product-des" data-title="Description">
												<p class="product-name"><a href="{{route('product-detail',$cart->product->slug)}}" target="_blank">{{$cart->product->title}}</a></p>
												<p class="product-des">{!!($cart['summary']) !!}</p>
											</td>

											<td class="price" data-title="Price"><span>{{number_format($cart->price, 0, ',', '.')}} đ</span></td>

											<td class="qty" data-title="Qty"><!-- Input Order -->
												<div class="input-group">
													<div class="button minus">
														<button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus" data-field="quant[{{$key}}]">
															<i class="ti-minus"></i>
														</button>
													</div>
													<input type="text" name="quant[{{$key}}]" class="input-number"  data-min="1" data-max="100" value="{{$cart->quantity}}">
													<input type="hidden" name="qty_id[]" value="{{$cart->id}}">
													<div class="button plus">
														<button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[{{$key}}]">
															<i class="ti-plus"></i>
														</button>
													</div>
												</div>
												<!--/ End Input Order -->
											</td>
											<td class="total-amount cart_single_price" data-title="Total"><span class="money">{{number_format($cart->amount, 0, ',', '.')}} đ</span></td>

											<td class="action" data-title="Remove"><a href="{{route('cart-delete',$cart->id)}}"><i class="ti-trash remove-icon"></i></a></td>
										</tr>
									@endforeach
									<track>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td class="float-right">
											<button class="btn float-right" type="submit">Cập nhật</button>
										</td>
									</track>
								@else
                                    <tr>
                                        <td class="text-center">Giỏ hàng trống...</td>
                                    </tr>
								@endif
							</form>
						</tbody>
					</table>
					<!--/ End Shopping Summery -->
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<!-- Total Amount -->
					<div class="total-amount">
						<div class="row">
							<div class="col-lg-8 col-md-5 col-12">
								<div class="left">
									<div class="coupon">
                                        <form action="{{route('coupon-store')}}" method="POST">
                                            @csrf
                                            <input name="code" placeholder="Nhập mã giảm giá">
                                            <button class="btn">Kích hoạt</button>
										</form>
									</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-7 col-12">
								<div class="right">
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
									<div class="button5">
										<a href="{{route('checkout')}}" class="btn">Thanh toán</a>
										<a href="{{route('product-grids')}}" class="btn">Tiếp tục mua sắm</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--/ End Total Amount -->
				</div>
			</div>
		</div>
	</div>
	<!--/ End Shopping Cart -->

    <x-commitment/>

	<!-- Start Shop Newsletter  -->
	@include('frontend.layouts.newsletter')
	<!-- End Shop Newsletter -->

@endsection
@push('styles')
	<style>
		li.shipping{
			display: inline-flex;
			width: 100%;
			font-size: 14px;
		}
		li.shipping .input-group-icon {
			width: 100%;
			margin-left: 10px;
		}
		.input-group-icon .icon {
			position: absolute;
			left: 20px;
			top: 0;
			line-height: 40px;
			z-index: 3;
		}
		.form-select {
			height: 30px;
			width: 100%;
		}
		.form-select .nice-select {
			border: none;
			border-radius: 0px;
			height: 40px;
			background: #f6f6f6 !important;
			padding-left: 45px;
			padding-right: 40px;
			width: 100%;
		}
		.list li{
			margin-bottom:0 !important;
		}
		.list li:hover{
			background:#F7941D !important;
			color:white !important;
		}
		.form-select .nice-select::after {
			top: 14px;
		}
	</style>
@endpush
@push('scripts')
	<script src="{{asset('frontend/js/nice-select/js/jquery.nice-select.min.js')}}"></script>
	<script src="{{ asset('frontend/js/select2/js/select2.min.js') }}"></script>
	<script>
		$(document).ready(function() { $("select.select2").select2(); });
  		$('select.nice-select').niceSelect();
	</script>
	<script>
		$(document).ready(function(){
			$('.shipping select[name=shipping]').change(function(){
				let cost = parseFloat( $(this).find('option:selected').data('price') ) || 0;
				let subtotal = parseFloat( $('.order_subtotal').data('price') );
				let coupon = parseFloat( $('.coupon_price').data('price') ) || 0;
				// alert(coupon);
				$('#order_total_price span').text((subtotal + cost-coupon).toFixed(0) + ' đ');
			});

		});

	</script>

@endpush
