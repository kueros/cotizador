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
						<div class="float-left">
							<a href="#" class="btn btn-outline-info float-right" data-placement="left" style="border-radius:20px;!important;margin-right:5px;">
								<i class="fas fa-list"></i> {{ __('Campos Adicionales') }}
							</a>
							<a href="#" class="btn btn-outline-primary float-right" data-placement="left" style="border-radius:20px;!important;margin-right:5px; ">
								<i class="fas fa-file-import"></i> {{ __('Importar Usuarios') }}
							</a>
							<a href="{{ route('users.create') }}" class="btn btn-outline-success float-right" data-placement="left" style="border-radius:20px;!important;margin-right:5px;">
								<i class="fas fa-plus"></i> {{ __('Agregar Usuario') }}
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
								<?php if ($user->habilitado == 2 || $user->habilitado == 0){ $user->habilitado = 'No'; } else { $user->habilitado = 'Si'; } ?>
									<tr>
										<td>{{ $user->nombre }}</td>
										<td>{{ $user->apellido }}</td>
										<td>{{ $user->username }}</td>
										<td>{{ $user->email }}</td>
										<td>{{ $user->nombre_rol }}</td>
										<td>{{ $user->habilitado }}</td>
										<td>{{ $user->bloqueado ? 'Sí' : 'No' }}</td>
										<td>
											<a class="btn btn-sm btn-outline-primary" title="Editar" href="{{ route('users.edit', $user->user_id) }}">
												<i class="fas fa-pencil-alt"></i>
											</a>
											<a class="btn btn-sm btn-outline-warning" href="javascript:void(0)" title="Deshabilitar" onclick="deshabilitar_usuario('{{ $user->user_id}}',0)">
												<i class="fas fa-lock"></i>
											</a>
											<a class="btn btn-sm btn-outline-warning" href="javascript:void(0)" title="Deshabilitar temporalmente" onclick="deshabilitar_usuario('{{ $user->user_id}}',2)">
												<span class="fas fa-clock"></span>
											</a>
											<a class="btn btn-sm btn-outline-info" href="javascript:void(0)" title="Blanquear" onclick="blanquear_psw('{{ $user->user_id }}')">
												<i class="fas fa-key"></i></a>
											<a class="btn btn-sm btn-outline-danger" title="Eliminar" href="{{ route('users.edit', $user->user_id) }}">
												<i class="fas fa-trash"></i>
											</a>
										</td>
												<!-- Botón para abrir el formulario de cambiar contraseña 
												<a href="{{ route('users.showPasswordForm', $user->user_id) }}" class="btn btn-warning btn-sm">
													{{ __('Cambiar contraseña') }}
												</a>
												-->
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


		<script>

	function deshabilitar_usuario(id, temporal = false) {
		var texto = temporal 
			? "¿Está seguro de que desea deshabilitar el usuario de forma temporal?" 
			: "¿Está seguro de que desea deshabilitar el usuario?";
		swal({
			title: texto,
			icon: "warning",
			buttons: true,
			dangerMode: true,
		}).then((confirmacion) => {
			if (confirmacion) {
				show_loading(); // Función personalizada que muestra un loader
				$.ajax({ headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
					url: "{{ route('users.deshabilitar_usuario', ':id') }}".replace(':id', id),
					type: "PATCH",
					data: {temporal: temporal},
					dataType: "JSON",
					success: function(data) {
						swal({
							title: "Aviso",
							text: "Usuario deshabilitado con éxito.",
							icon: "success"
						}).then(() => {
							// Recargar la tabla DataTables al cerrar el modal de éxito
							location.reload();
						});
						hide_loading(); // Función personalizada que oculta el loader
					},
					error: function (jqXHR, textStatus, errorThrown) {
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});
			}
		});
	}

	function blanquear_psw(id)
	{
		swal({
			title: "¿Desea blanquear la contraseña del usuario?",
			icon: "warning",
			buttons: true,
			dangerMode: true,
		}).then((confirmacion) => {
			if (confirmacion) {
				show_loading();
				$.ajax({ 
					headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
					url: "{{ route('users.blanquear_password', ':id') }}".replace(':id', id),
					type: "PATCH",
					dataType: "JSON",
					success: function(data) {
						swal({
							title: "Aviso",
							text: "Contraseña blanqueada con éxito.",
							icon: "success"
						}).then(() => {
							// Recargar la tabla DataTables al cerrar el modal de éxito
							location.reload();
						});
						hide_loading(); // Función personalizada que oculta el loader
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});
			}
		});
	}

		</script>
	</x-app-layout>
