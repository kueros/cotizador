<section>
	<header>
		<h2 class="text-lg font-medium text-gray-900">
			{{ __('Editar Usuario') }}
		</h2>

		<p class="mt-1 text-sm text-gray-600">
			{{ __('Edición de usuarios.') }}
		</p>
	</header>
<?php #dd($roles); ?>
	<form method="post" action="{{ route('users.update', $users->user_id) }}" class="mt-6 space-y-6">
		@csrf
		@method('patch')
		<?php #dd(route('users.update', $users->user_id)); ?>

		@if ($errors->any())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<div>
			<x-input-label for="nombre" :value="__('Nombre')" />
			<x-text-input id="nombre" value="{{ $users->nombre }}" name="nombre" type="text" class="mt-1 block w-full" autocomplete="nombre" />
			<x-input-error :messages="$errors->get('nombre')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="apellido" :value="__('Apellido')" />
			<x-text-input id="apellido" value="{{ $users->apellido }}" name="apellido" type="text" class="mt-1 block w-full" autocomplete="apellido" />
			<x-input-error :messages="$errors->get('apellido')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="username" :value="__('Username')" />
			<x-text-input id="username" value="{{ $users->username }}" name="username" type="text" class="mt-1 block w-full" autocomplete="username" />
			<x-input-error :messages="$errors->get('username')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="email" :value="__('Email')" />
			<x-text-input id="email" value="{{ $users->email }}" name="email" type="email" class="mt-1 block w-full" autocomplete="Email" />
			<x-input-error :messages="$errors->get('email')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="rol_id" :value="__('Rol')" />
			<select id="rol_id" name="rol_id" class="mt-1 block w-full">
				<option value="0" {{ old('rol_id', $users->rol_id) === null ? 'selected' : '' }}>
					{{ __('Elija un Rol') }}
				</option>
				@foreach($roles as $rol)
				<option value="{{ $rol->rol_id }}" {{ old('rol_id', $users->rol_id) == $rol->rol_id ? 'selected' : '' }}>
					{{ $rol->nombre }}
				</option>
				@endforeach
			</select>
			<x-input-error :messages="$errors->get('rol_id')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="habilitado" :value="__('Habilitado')" />
			<div class="mt-1">
				<label>
					<input type="radio" name="habilitado" value="1" {{ $users->habilitado == 1 ? 'checked' : '' }} >
					Sí
				</label>
				<label class="ml-4">
					<input type="radio" name="habilitado" value="0" {{ $users->habilitado == 0 ? 'checked' : '' }} >
					No
				</label>
			</div>
		</div>
		<div style="display: none;">
			<x-input-label for="bloqueado" :value="__('Bloqueado')" />
			<x-text-input id="bloqueado" value="{{ $users->bloqueado }}" name="bloqueado" type="text" class="mt-1 block w-full" autocomplete="bloqueado" />
			<x-input-error :messages="$errors->get('bloqueado')" class="mt-2" />
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

