<section>
	<header>

		<div class="container-full-width">
			<div class="row">
				<div class="col-md-12">
					<h2>Roles</h2>
				</div>
			</div>
		</div>
	</header>

	<form method="post" action="{{ route('roles.store') }}" class="mt-6 space-y-6">
		@csrf

		<div>
			<x-input-label for="nombre" :value="__('Nombre')" />
			<x-text-input id="nombre" value="{{ old('nombre', $roles->nombre) }}" name="nombre" type="text" class="mt-1 block w-full" placeholder="Nombre del Rol" />
			<x-input-error :messages="$errors->get('nombre')" class="mt-2" />
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