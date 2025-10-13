<!DOCTYPE html>
<html lang="zxx">
<head>
	@include('frontend.layouts.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pXQ2X7k6kL3YJqQk6Kk7p4qk3qz0X+q5L1zK2e7wK0V6b9Y8z0F5k+YQp6kQ9b5g1qX6a9k+3eJm0Y1g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @livewireStyles
</head>
<body class="js">

	<!-- Preloader -->
	<div class="preloader">
		<div class="preloader-inner">
			<div class="preloader-icon">
				<span></span>
				<span></span>
			</div>
		</div>
	</div>
	<!-- End Preloader -->

    <x-alert-container/>

	<!-- Header -->
	@include('frontend.layouts.header')
	<!--/ End Header -->
	@yield('main-content')
    <x-chatbot />
	@include('frontend.layouts.footer')
    @livewireScripts
</body>
</html>
