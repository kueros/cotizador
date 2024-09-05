<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'YAFO') }}</title>
	<style>
		body {
			background-image: url('/build/assets/images/login-background.jpg');
			background-size: cover;
			/* Hace que la imagen cubra todo el área */
			background-repeat: no-repeat;
			/* Evita que la imagen se repita */
			background-position: center;
			/* Centra la imagen */
		}
	</style>

	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

	<!-- Scripts -->
	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
	<div class="min-h-screen flex flex-col items-center">
		<!-- 		<div>
			<a href="/">
				<x-application-logo class="w-20 h-20 fill-current text-gray-500" />
			</a>
		</div>
 -->
		<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg" style=" background-color: rgba(8, 34, 71, 0.82); margin-top: 100px;">
			{{ $slot }}
		</div>
	</div>
</body>

</html>
