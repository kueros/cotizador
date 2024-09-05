<section>
	<header>
		<h2 class="text-lg font-medium text-gray-900">
			{{ __('Editar Usuario') }}
		</h2>

		<p class="mt-1 text-sm text-gray-600">
			{{ __('Edición de usuarios.') }}
		</p>
	</header>

	<form method="post" action="{{ route('users.update', $user->id) }}" class="mt-6 space-y-6">
		@csrf
		@method('patch')

		<div>
			<x-input-label for="username" :value="__('Nombre de Usuario')" />
			<x-text-input id="username" value="{{ $user->username }}" name="username" type="text" class="mt-1 block w-full" autocomplete="username" />
			<x-input-error :messages="$errors->updatePassword->get('username')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="nombre" :value="__('Nombre')" />
			<x-text-input id="nombre" value="{{ $user->nombre }}" name="nombre" type="text" class="mt-1 block w-full" autocomplete="nombre" />
			<x-input-error :messages="$errors->updatePassword->get('nombre')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="apellido" :value="__('Apellido')" />
			<x-text-input id="apellido" value="{{ $user->apellido }}" name="apellido" type="text" class="mt-1 block w-full" autocomplete="apellido" />
			<x-input-error :messages="$errors->updatePassword->get('apellido')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="email" :value="__('Email')" />
			<x-text-input id="email" value="{{ $user->email }}" name="email" type="text" class="mt-1 block w-full" autocomplete="email" />
			<x-input-error :messages="$errors->updatePassword->get('email')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="habilitado" :value="__('Habilitado')" />
			<x-text-input id="habilitado" value="{{ $user->habilitado }}" name="habilitado" type="text" class="mt-1 block w-full" autocomplete="habilitado" />
			<x-input-error :messages="$errors->updatePassword->get('habilitado')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="bloqueado" :value="__('Bloqueado')" />
			<x-text-input id="bloqueado" value="{{ $user->bloqueado }}" name="bloqueado" type="text" class="mt-1 block w-full" autocomplete="bloqueado" />
			<x-input-error :messages="$errors->updatePassword->get('bloqueado')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="ultimo_login" :value="__('Ultimo Login')" />
			<x-text-input id="ultimo_login" value="{{ $user->ultimo_login }}" name="ultimo_login" type="text" class="mt-1 block w-full" autocomplete="ultimo_login" />
			<x-input-error :messages="$errors->updatePassword->get('ultimo_login')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="intentos_login" :value="__('Intentos de Login')" />
			<x-text-input id="intentos_login" value="{{ $user->intentos_login }}" name="intentos_login" type="text" class="mt-1 block w-full" autocomplete="intentos_login" />
			<x-input-error :messages="$errors->updatePassword->get('intentos_login')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="created_at" :value="__('Fecha de Creacion')" />
			<x-text-input id="created_at" value="{{ $user->created_at }}" name="created_at" type="text" class="mt-1 block w-full" autocomplete="new-password" />
			<x-input-error :messages="$errors->updatePassword->get('created_at')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="updated_at" :value="__('Fecha de Modificacion')" />
			<x-text-input id="updated_at" value="{{ $user->updated_at }}" name="updated_at" type="text" class="mt-1 block w-full" autocomplete="new-password" />
			<x-input-error :messages="$errors->updatePassword->get('updated_at')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="deleted_at" :value="__('Fecha de Eliminacion')" />
			<x-text-input id="deleted_at" value="{{ $user->deleted_at }}" name="deleted_at" type="text" class="mt-1 block w-full" autocomplete="new-password" />
			<x-input-error :messages="$errors->updatePassword->get('deleted_at')" class="mt-2" />
		</div>

		<div class="flex items-center gap-4">
			<x-primary-button>{{ __('Save') }}</x-primary-button>

			@if (session('status') === 'profile-updated')
			<p
				x-data="{ show: true }"
				x-show="show"
				x-transition
				x-init="setTimeout(() => show = false, 2000)"
				class="text-sm text-gray-600">{{ __('Saved.') }}</p>
			@endif
		</div>
	</form>
</section>