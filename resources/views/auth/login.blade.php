<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>YAFO</title>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

	<style>
		body {
			transform: scale(0.75); /* Ajusta el tamaño general al 80% */
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
	<link href="/build/assets/css/exvite.css" rel="stylesheet">

	<!-- Scripts -->
</head>

<body class="font-sans text-gray-900 antialiased">

	<div class="min-h-screen flex flex-col items-center">
		<div class="w-full smmax-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm rounded-lg" style=" background-color: rgba(8, 34, 71, 0.82); margin-top: 250px; width: 36rem;">

			<!-- Session Status -->
			<!-- x-auth-session-status class="mb-4" :status="session('status')" / -->

			<form method="POST" action="{{ secure_url('login') }}">
				@csrf

				<div>
					<a href="/">
						<img class="logo-home" src="/build/assets/images/aleph_logo.gif" alt="" title="">
					</a>
				</div>
				<!-- Manejo de errores -->
				<div style="margin-top:1rem;">
					@if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
					@endif
					@if (session('error')) 
					<div class="alert alert-danger">
						{{ session('error') }}
					</div>
					@endif
					@if (session('success'))
					<div class="alert alert-success" role="alert">
							{{ session('success') }}
						</div>
					@endif
				</div>
				<!-- Email Address -->
				<div>
					<label for="email" value="__('Email o Username')" class="text-white mt-12 w-full">
						<input id="email" class="block mt-1 w-full text-black" type="text" name="email" value="{{ old('email') }}" placeholder="Email o Username" required autofocus autocomplete="username" />
						<input-error messages="$errors->get('email')" class="mt-2" />
					</label>
				</div>
				<!-- Password -->
				<div class="mt-4">
					<label for="password" value="__('Password')" class="text-white mt-12 w-full">
						<input id="password" class="block mt-1 w-full text-black" type="password" name="password" required autocomplete="current-password" />
						<input-error messages="$errors->get('password')" class="mt-2" />
				</div>

				<!-- Remember Me -->
				<div class="block mt-4">
					<label for="remember_me" class="inline-flex items-center">
						<span id="show_password" class="ms-2 text-sm text-white">
						<input type="checkbox" value="1" onchange="show_password(this)" /> Mostrar contraseña</span>
					</label>
				</div>

				<div class="flex items-center justify-between mt-4">
					<!-- Forgot Password Link -->
					<a class="underline text-sm text-white hover:text-gray-200" href="{{ route('password.request') }}">
						{{ __('Olvidé mi contraseña') }}
					</a>
				</div>
				<div class="flex itemsw-full smmax-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm rounded-lg">
					<button class="btn btn-primary btn-lg ms-12 text-white w-full" >
						Iniciar Sesión
					</button>
				</div>
			</form>
		</div>
	</div>
</body>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<script src="/build/assets/js/jquery-ui.js"></script>
	<script>
		function show_password(elemento){

			var x = document.getElementById("password");

			if($(elemento).prop('checked')){
				x.type = "text";
			}else{
				x.type = "password";
			}
		}
	</script>

</html>