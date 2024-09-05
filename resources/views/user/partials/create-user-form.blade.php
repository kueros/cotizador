<section>
	<header>
		<h2 class="text-lg font-medium text-gray-900">
			{{ __('Editar Usuario') }}
		</h2>

		<p class="mt-1 text-sm text-gray-600">
			{{ __('Edición de usuarios.') }}
		</p>
	</header>

	<form method="post" action="{{ route('users.create') }}" class="mt-6 space-y-6">
		@csrf
		@method('get')

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