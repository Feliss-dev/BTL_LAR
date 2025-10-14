@extends('frontend.layouts.master')

@section('title', 'E-SHOP || Register Page')

@section('main-content')
    <!-- Shop Login -->
    <section class="shop login section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 col-12">
                    <h1 class="text-center font-weight-bold">Cảm ơn vì đã đăng ký</h1>

                    <p class="text-center">Trước khi bắt đầu, vui lòng xác thực email bằng cách bấm nút bên dưới.</p>

                    <form action="{{route('verification.send')}}" method="post" class="text-center mt-4">
                        @csrf

                        <button type="submit" class="btn-success" style="padding: 1.25rem 0.75rem">Gửi Email xác thực</button>
                    </form>

                    @if ($message = session()->get('message'))
                        <p class="text-center text-green-500 mt-2">{{$message}}</p>
                    @endif

{{--                    <div class="login-form">--}}
{{--                        <h2>Đăng ký</h2>--}}
{{--                        <p>Hãy đăng ký để có thể cùng mua hàng nhanh chóng</p>--}}
{{--                        <!-- Form -->--}}
{{--                        <form class="form" method="post" action="{{ route('register.submit') }}">--}}
{{--                            @csrf--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label>Tên<span>*</span></label>--}}
{{--                                        <input type="text" name="name" placeholder="" required="required"--}}
{{--                                               value="{{ old('name') }}">--}}
{{--                                        @error('name')--}}
{{--                                        <span class="text-danger">{{ $message }}</span>--}}
{{--                                        @enderror--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label>Email<span>*</span></label>--}}
{{--                                        <input type="text" name="email" placeholder="" required="required"--}}
{{--                                               value="{{ old('email') }}">--}}
{{--                                        @error('email')--}}
{{--                                        <span class="text-danger">{{ $message }}</span>--}}
{{--                                        @enderror--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label>Mật khẩu<span>*</span></label>--}}
{{--                                        <input type="password" name="password" placeholder="" required="required"--}}
{{--                                               value="{{ old('password') }}">--}}
{{--                                        @error('password')--}}
{{--                                        <span class="text-danger">{{ $message }}</span>--}}
{{--                                        @enderror--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label>Xác nhận mật khẩu<span>*</span></label>--}}
{{--                                        <input type="password" name="password_confirmation" placeholder=""--}}
{{--                                               required="required" value="{{ old('password_confirmation') }}">--}}
{{--                                        @error('password_confirmation')--}}
{{--                                        <span class="text-danger">{{ $message }}</span>--}}
{{--                                        @enderror--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                                <div class="col-12 mt-2">--}}
{{--                                    <div class="form-group login-btn d-flex justify-content-center flex-column">--}}
{{--                                        <button class="btn" type="submit">Đăng ký</button>--}}

{{--                                        <p class="mt-2">--}}
{{--                                            Đã có tài khoản? <a href="{{ route('login.form') }}">Nhấn vào đây để đăng nhập</a>--}}
{{--                                        </p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                        <!--/ End Form -->--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>
    </section>
    <!--/ End Login -->
@endsection

@push('styles')
    <style>
        .shop.login .form .btn {
            margin-right: 0;
        }

        .btn-facebook {
            background: #39579A;
        }

        .btn-facebook:hover {
            background: #073088 !important;
        }

        .btn-github {
            background: #444444;
            color: white;
        }

        .btn-github:hover {
            background: black !important;
        }

        .btn-google {
            background: #ea4335;
            color: white;
        }

        .btn-google:hover {
            background: rgb(243, 26, 26) !important;
        }
    </style>
@endpush
