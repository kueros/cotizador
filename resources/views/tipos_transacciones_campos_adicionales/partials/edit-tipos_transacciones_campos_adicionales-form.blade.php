<section>
	<header>
		<h2 class="text-lg font-medium text-gray-900">
			{{ __('Editar Campo Adcional') }}
		</h2>

		<p class="mt-1 text-sm text-gray-600">
			{{ __('Edición de campos adicionales.') }}
		</p>
	</header>
	@php
	#dd($tipos_transacciones_campos_adicionales);
	@endphp
	<form method="post" action="{{ route('tipos_transacciones_campos_adicionales.update', $tipos_transacciones_campos_adicionales->id) }}" class="mt-6 space-y-6">
		@csrf
		@method('patch')

		<div>
			<x-input-label for="nombre_campo" :value="__('Nombre')" />
			<x-text-input id="nombre_campo" value="{{ $tipos_transacciones_campos_adicionales->nombre_campo }}" name="nombre_campo" type="text" class="mt-1 block w-full" autocomplete="nombre_campo" />
			<x-input-error :messages="$errors->get('nombre_campo')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="nombre_mostrar" :value="__('Nombre a Mostrar')" />
			<x-text-input id="nombre_mostrar" value="{{ $tipos_transacciones_campos_adicionales->nombre_mostrar }}" name="nombre_mostrar" type="text" class="mt-1 block w-full" autocomplete="nombre_mostrar" />
			<x-input-error :messages="$errors->get('nombre_mostrar')" class="mt-2" />
		</div>

		<div class="form-body">
			<div class="mb-3 row">
				<label class="col-form-label col-md-2">{{ __('Tipo de Campo') }}</label>
				<div class="col-md-3">
					<select id="tipo" name="tipo" class="mt-1 block form-control" required>
						<option value="0">
							{{ $tipos_transacciones_campos_adicionales->tipo_nombre }}
						</option>
						@foreach($tipos_campos as $tipo_campo)
						<option value="{{ $tipo_campo->id }}">
							{{ $tipo_campo->nombre }}
						</option>
						@endforeach
					</select>
					<span class="help-block"></span>
				</div>
			</div>
		</div>

		<div>
			<x-input-label for="orden_listado" :value="__('Orden en el formulario')" />
			<x-text-input id="orden_listado" value="{{ $tipos_transacciones_campos_adicionales->orden_listado }}" name="orden_listado" type="number" class="mt-1 block w-full" autocomplete="orden_listado" />
			<x-input-error :messages="$errors->get('orden_listado')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="visible" :value="__('Visible')" />
			<x-text-input id="visible" value="{{ $tipos_transacciones_campos_adicionales->visible }}" name="visible" type="checkbox" class="mt-1 block w-full" autocomplete="visible" />
			<x-input-error :messages="$errors->get('visible')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="requerido" :value="__('Requerido')" />
			<x-text-input id="requerido" value="{{ $tipos_transacciones_campos_adicionales->requerido }}" name="requerido" type="checkbox" class="mt-1 block w-full" autocomplete="requerido" />
			<x-input-error :messages="$errors->get('nombre')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="valor_default" :value="__('Valor por Default')" />
			<x-text-input id="valor_default" value="{{ $tipos_transacciones_campos_adicionales->valor_default }}" name="valor_default" type="text" class="mt-1 block w-full" autocomplete="valor_default" />
			<x-input-error :messages="$errors->get('valor_default')" class="mt-2" />
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