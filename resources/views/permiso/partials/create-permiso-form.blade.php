<section>
	<header>

		<div class="container-full-width">
			<div class="row">
				<div class="col-md-12">
					<h2>Permisos</h2>
				</div>
			</div>
		</div>
	</header>

	<form method="post" action="{{ route('permisos.store') }}" class="mt-6 space-y-6">
		@csrf
<?php #dd($permisoUltimoId); ?>
		<div>
			<x-input-label for="id" :value="__('Id')" />
			<x-text-input id="id" value="{{ $permisoUltimoId + 1 }}" name="id" type="text" class="mt-1 block w-full" placeholder="Id del Permiso" />
			<x-input-error :messages="$errors->get('id')" class="mt-2" />
		</div>
		<div>
			<x-input-label for="nombre" :value="__('Nombre')" />
			<x-text-input id="nombre" value="{{ old('nombre', $permisos->nombre) }}" name="nombre" type="text" class="mt-1 block w-full" placeholder="Nombre del Permiso" />
			<x-input-error :messages="$errors->get('nombre')" class="mt-2" />
		</div>
		<div>
			<x-input-label for="descripcion" :value="__('Descripción')" />
			<x-text-input id="descripcion" value="{{ old('descripcion', $permisos->descripcion) }}" name="descripcion" type="text" class="mt-1 block w-full" placeholder="Descripción" />
			<x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
		</div>
		<div>
			<x-input-label for="orden" :value="__('Orden')" />
			<x-text-input id="orden" value="{{ old('orden', $permisos->orden) }}" name="orden" type="text" class="mt-1 block w-full" placeholder="Orden" />
			<x-input-error :messages="$errors->get('orden')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="seccion_id" :value="__('Sección')" />
			<select id="seccion_id" name="seccion_id" class="mt-1 block w-full">
				<option value="0" {{ old('seccion_id', $permisos->seccion_id) === null ? 'selected' : '' }}>
					{{ __('Elija una Sección') }}
				</option>
				@foreach($secciones as $seccion)
				<option value="{{ $seccion->seccion_id }}" {{ old('seccion_id', $seccion->seccion_id) == $seccion->seccion_id ? 'selected' : '' }}>
					{{ $seccion->nombre }}
				</option>
				@endforeach
			</select>
			<x-input-error :messages="$errors->get('rol_id')" class="mt-2" />
		</div>


		<div class="flex items-center gap-4">
			<x-primary-button>{{ __('Guardar') }}</x-primary-button>

			@if (session('status') === 'profile-updated')
			<p
				x-data="{ show: true }"
				x-show="show"
				x-transition
				x-init="setTimeout(() => show = false, 2000)"
				class="text-sm text-gray-600">{{ __('Guardado.') }}</p>
			@endif
		</div>
	</form>
</section>