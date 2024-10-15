<section>
	<header>
		<h2 class="text-lg font-medium text-gray-900">
			{{ __('Editar Usuario') }}
		</h2>

		<p class="mt-1 text-sm text-gray-600">
			{{ __('Edición de usuarios.') }}
		</p>
	</header>

	<form method="post" action="{{ route('users.update', $users->user_id) }}" class="mt-6 space-y-6">
		@csrf
		@method('patch')
<?php #dd(route('users.update', $users->user_id)); ?>
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
			<x-input-label for="rol_id" :value="__('Rol')" />
			
			<select id="rol_id" name="rol_id" class="mt-1 block w-full">
				@foreach($roles as $rol)
					<option value="{{ $rol->id }}" {{ $users->rol_id == $rol->id ? 'selected' : '' }}>
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
					<input type="radio" name="habilitado" value="1" {{ $users->habilitado == 1 ? 'checked' : '' }} >
					Sí
				</label>
				<label class="ml-4">
					<input type="radio" name="habilitado" value="0" {{ $users->habilitado == 0 ? 'checked' : '' }} >
					No
				</label>
			</div>
			<!-- Campo oculto para enviar el valor de "bloqueado" -->
			<input type="hidden" name="habilitado" value="{{ $users->habilitado }}">
			<x-input-error :messages="$errors->get('habilitado')" class="mt-2" />
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

