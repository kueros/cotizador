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
			<x-text-input id="username" value="{{ old('username', $user->username) }}" name="username" type="text" class="mt-1 block w-full" autocomplete="username" />
			<x-input-error :messages="$errors->updatePassword->get('username')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="nombre" :value="__('Nombre')" />
			<x-text-input id="nombre" value="{{ old('nombre', $user->nombre) }}" name="nombre" type="text" class="mt-1 block w-full" autocomplete="nombre" />
			<x-input-error :messages="$errors->updatePassword->get('nombre')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="apellido" :value="__('Apellido')" />
			<x-text-input id="apellido" value="{{ old('apellido', $user->apellido) }}" name="apellido" type="text" class="mt-1 block w-full" autocomplete="apellido" />
			<x-input-error :messages="$errors->updatePassword->get('apellido')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="email" :value="__('Email')" />
			<x-text-input id="email" value="{{ old('email', $user->email) }}" name="email" type="text" class="mt-1 block w-full" autocomplete="email" />
			<x-input-error :messages="$errors->updatePassword->get('email')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="rol_id" :value="__('Rol')" />
			<select id="rol_id" name="rol_id" class="mt-1 block w-full">
				@foreach($roles as $rol)
					<option value="{{ $rol->id }}" {{ old('rol_id', $user->rol_id) == $rol->id ? 'selected' : '' }}>
						{{ $rol->nombre }}
					</option>
				@endforeach
			</select>

			<x-input-error :messages="$errors->updatePassword->get('rol_id')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="habilitado" :value="__('Habilitado')" />
			<div class="mt-1">
				<label>
					<input type="radio" name="habilitado" value="1" {{ old('habilitado', $user->habilitado) == 1 ? 'checked' : '' }}>
					Sí
				</label>
				<label class="ml-4">
					<input type="radio" name="habilitado" value="0" {{ old('habilitado', $user->habilitado) == 0 ? 'checked' : '' }}>
					No
				</label>
			</div>
			<x-input-error :messages="$errors->updatePassword->get('habilitado')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="bloqueado" :value="__('Bloqueado')" />
			<div class="mt-1">
				<label>
					<input type="radio" name="bloqueado" value="1" {{ old('bloqueado', $user->bloqueado) == 1 ? 'checked' : '' }}>
					Sí
				</label>
				<label class="ml-4">
					<input type="radio" name="bloqueado" value="0" {{ old('bloqueado', $user->bloqueado) == 0 ? 'checked' : '' }}>
					No
			<x-input-error :messages="$errors->updatePassword->get('bloqueado')" class="mt-2" />
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