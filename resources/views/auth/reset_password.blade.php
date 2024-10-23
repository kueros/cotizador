<x-app-layout>

<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Restablecer Contraseña1') }}
		</h2>
	</x-slot>
<?php #dd($email); ?>
	<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
		<div class="p-12 bg-white shadow sm:rounded-lg">
			<form method="POST" action="{{ route('resetear_password') }}">
				@csrf

				<input type="hidden" name="token" value="{{ $token }}">
				<input type="hidden" name="email" value="{{ $email }}">
				<!-- Nueva Contraseña -->
				<div class="mt-4">
					<x-input-label for="password" value="{{ __('Ingrese una contraseña que tenga como mínimo 8 caracteres de longitud (debe contener: 1 caracter especial, 1 número, 1 letra en mayúsculas, 1 letra en minúsculas)') }}" />
					<x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4" required="" minlength="8" maxlength="20"  />
					<x-input-error :messages="$errors->get('password')" class="mt-2" />
				</div>
				<!-- Confirmar Contraseña -->
				<div class="mt-4">
					<x-input-label for="password_confirmation" value="{{ __('Confirmar Contraseña') }}" />
					<x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-3/4" required="" minlength="8" maxlength="20"  />
					<x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
				</div>


				<div class="mt-6 flex justify-end">
					<x-danger-button>
						{{ __('Restablecer Contraseña') }}
					</x-danger-button>
				</div>
			</form>
		</div>
	</div>


</x-app-layout>