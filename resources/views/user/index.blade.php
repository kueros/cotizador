<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Usuarios') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard_bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

			<!-- Tabla de usuarios -->
			<div class="p-12 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="table-responsive">
					<div class="float-right">
						<a href="{{ route('users.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
							{{ __('Nuevo Usuario') }}
						</a>
					</div>
					<table id="example" class="cell-border" style="width:100%">
						<thead class="thead">
							<tr>
								<th>Nombre</th>
								<th>Apellido</th>
								<th>Username</th>
								<th>Email</th>
								<th>Rol</th>
								<th>Habilitado</th>
								<th>Bloqueado</th>
								<th colspan="2" class="text-center">Acciones</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($users as $user)
								<tr>
									<td>{{ $user->nombre }}</td>
									<td>{{ $user->apellido }}</td>
									<td>{{ $user->username }}</td>
									<td>{{ $user->email }}</td>
									<td>{{ $user->nombre_rol }}</td>
									<td>{{ $user->habilitado ? 'Sí' : 'No' }}</td>
									<td>{{ $user->bloqueado ? 'Sí' : 'No' }}</td>
									<td>
											<a class="btn btn-sm btn-success" href="{{ route('users.edit', $user->user_id) }}">
												<i class="fa fa-fw fa-edit" title="Editar" ></i>
											</a>

											<!-- Botón para abrir el formulario de cambiar contraseña -->
											<a href="{{ route('users.showPasswordForm', $user->user_id) }}" class="btn btn-warning btn-sm">
												{{ __('Cambiar contraseña') }}
											</a>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<!-- Mostrar formulario de cambio de contraseña si el usuario está establecido -->
			@if(isset($selectedUser))
				<div class="p-12 sm:p-8 bg-white shadow sm:rounded-lg">
					<h2 class="text-lg font-medium text-gray-900">
						{{ __('Cambiar contraseña de ') . $selectedUser->nombre }}
					</h2>

					<form method="post" action="{{ route('password.update', $selectedUser->user_id) }}" class="p-6">
						@csrf
						@method('patch')

						<!-- Campo para nueva contraseña -->
						<div class="mt-4">
							<x-input-label for="password" value="{{ __('Nueva Contraseña') }}" />
							<x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4" placeholder="{{ __('Nueva Contraseña') }}" />
							<x-input-error :messages="$errors->get('password')" class="mt-2" />
						</div>

						<!-- Confirmar nueva contraseña -->
						<div class="mt-4">
							<x-input-label for="password_confirmation" value="{{ __('Confirmar Contraseña') }}" />
							<x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-3/4" placeholder="{{ __('Confirmar Contraseña') }}" />
							<x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
						</div>

						<div class="mt-6 flex justify-end">
							<a href="{{ route('users.index') }}" class="btn btn-secondary">
								{{ __('Cancelar') }}
							</a>

							<x-danger-button class="ml-3">
								{{ __('Cambiar contraseña') }}
							</x-danger-button>
						</div>
					</form>
				</div>
			@endif

		</div>
	</div>
</x-app-layout>
