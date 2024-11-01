<section>
	<header>
		<h2 class="text-lg font-medium text-gray-900">
			{{ __('Editar Permiso') }}
		</h2>

		<p class="mt-1 text-sm text-gray-600">
			{{ __('Edición de permisos.') }}
		</p>
	</header>

	<form method="post" action="{{ route('permisos.update', $permiso->id) }}" class="mt-6 space-y-6">
		@csrf
		@method('patch')

		<div>
			<x-input-label for="id" :value="__('ID')" />
			<x-text-input id="id" value="{{ $permiso->id }}" name="id" type="text" class="mt-1 block w-full" autocomplete="id" />
			<x-input-error :messages="$errors->get('id')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="nombre" :value="__('Nombre')" />
			<x-text-input id="nombre" value="{{ $permiso->nombre }}" name="nombre" type="text" class="mt-1 block w-full" autocomplete="nombre" />
			<x-input-error :messages="$errors->get('nombre')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="descripcion" :value="__('Descripción')" />
			<x-text-input id="descripcion" value="{{ $permiso->descripcion }}" name="descripcion" type="text" class="mt-1 block w-full" autocomplete="descripcion" />
			<x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="orden" :value="__('Orden')" />
			<x-text-input id="orden" value="{{ $permiso->orden }}" name="orden" type="text" class="mt-1 block w-full" autocomplete="orden" />
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