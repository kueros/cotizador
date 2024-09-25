<section>
	<header>

		<div class="container-full-width">
			<div class="row">
				<div class="col-md-12">
					<h2>Usuarios</h2>
				</div>
			</div>
		</div>
	</header>

	<form method="post" action="{{ route('users.store') }}" class="mt-6 space-y-6">
	@csrf

	<div>
		<x-input-label for="username" :value="__('Nombre de Usuario')" />
		<x-text-input id="username" value="{{ old('username', $user->username) }}" name="username" type="text" class="mt-1 block w-full" placeholder="Nombre de Usuario" />
		<x-input-error :messages="$errors->get('username')" class="mt-2" />
	</div>

	<div>
		<x-input-label for="nombre" :value="__('Nombre')" />
		<x-text-input id="nombre" value="{{ old('nombre', $user->nombre) }}" name="nombre" type="text" class="mt-1 block w-full" placeholder="Nombre" />
		<x-input-error :messages="$errors->get('nombre')" class="mt-2" />
	</div>

	<div>
		<x-input-label for="apellido" :value="__('Apellido')" />
		<x-text-input id="apellido" value="{{ old('apellido', $user->apellido) }}" name="apellido" type="text" class="mt-1 block w-full" placeholder="Apellido" />
		<x-input-error :messages="$errors->get('apellido')" class="mt-2" />
	</div>

	<div>
		<x-input-label for="email" :value="__('Email')" />
		<x-text-input id="email" value="{{ old('email', $user->email) }}" name="email" type="email" class="mt-1 block w-full" placeholder="Email" />
		<x-input-error :messages="$errors->get('email')" class="mt-2" />
	</div>

	<div>
		<x-input-label for="habilitado" :value="__('Habilitado')" />
		<x-text-input id="habilitado" value="{{ old('habilitado', $user->habilitado) }}" name="habilitado" type="text" class="mt-1 block w-full" placeholder="Habilitado" />
		<x-input-error :messages="$errors->get('habilitado')" class="mt-2" />
	</div>

	<div class="flex items-center gap-4">
		<x-primary-button>{{ __('Guardar') }}</x-primary-button>

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