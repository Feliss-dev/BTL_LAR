<!DOCTYPE html>
<html lang="zxx">
<head>
	@include('frontend.layouts.head')

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

	@include('frontend.layouts.footer')
    @livewireScripts
</body>
</html>
