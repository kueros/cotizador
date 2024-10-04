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

		<div>
			<x-input-label for="nombre" :value="__('Nombre')" />
			<x-text-input id="nombre" value="{{ old('nombre', $permisos->nombre) }}" name="nombre" type="text" class="mt-1 block w-full" placeholder="Nombre del Permiso" />
			<x-input-error :messages="$errors->get('nombre')" class="mt-2" />
		</div>
		<div>
			<x-input-label for="registra_log" :value="__('Registra Log')" />
			<x-text-input id="registra_log" value="{{ old('registra_log', $permisos->registra_log) }}" name="registra_log" type="text" class="mt-1 block w-full" placeholder="Registra Log" />
			<x-input-error :messages="$errors->get('registra_log')" class="mt-2" />
		</div>
		<div>
			<x-input-label for="orden" :value="__('Orden')" />
			<x-text-input id="orden" value="{{ old('orden', $permisos->orden) }}" name="orden" type="text" class="mt-1 block w-full" placeholder="Orden" />
			<x-input-error :messages="$errors->get('orden')" class="mt-2" />
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