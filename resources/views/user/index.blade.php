<x-app-layout title="Usuarios" :breadcrumbs="[['title' => 'Inicio', 'url' => route('dashboard')]]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Usuarios') }}
		</h2>
	</x-slot>
	@php
		$user = Auth::user()->username;
		$email = Auth::user()->email;
		$permiso_agregar_usuarios = tiene_permiso('add_usr');
		$permiso_editar_usuarios = tiene_permiso('edit_usr');
		$permiso_deshabilitar_permisos = tiene_permiso('enable_usr');
		$permiso_blanquear_password = tiene_permiso('clean_pass');
		$permiso_borrar_usuarios = tiene_permiso('del_usr');
		$permiso_importar_usuarios = tiene_permiso('import_usr');
		#dd($configurar_claves);
	@endphp
	<script type="text/javascript">

		jQuery(document).ready(function($){
			table = $('#usuarios-table').DataTable({
				dom: 'Bfrtip',
				buttons: [
					{"extend": 'pdf', "text":'Export',"className": 'btn btn-danger', "orientation": 'landscape', title: 'Usuarios'},
					{"extend": 'copy', "text":'Export',"className": 'btn btn-primary', title: 'Usuarios'},
					{"extend": 'excel', "text":'Export',"className": 'btn btn-success', title: 'Usuarios'},
					{"extend": 'print', "text":'Export',"className": 'btn btn-secondary', title: 'Usuarios'}
				],
				initComplete: function () {
					$('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
					$('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
					$('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
					$('.buttons-print').html('<span class="bi bi-printer" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir');
				}
			});
			/*$("#importador_form_usuarios").submit(function(e) {
				e.preventDefault(); // avoid to execute the actual submit of the form.

				show_loading();   
				var formData = new FormData(this);

				$.ajax({
					type: "POST",
					url: "{{ url('usuarios/ajax_importador_submit') }}",
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(data){
						hide_loading();
						location.reload();
					}, 
					error: function (jqXHR, textStatus, errorThrown){
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});
			});

			//var filtrar_usuarios = '';

			table = $('#usuarios-table').DataTable({
				"ajax": {
					url : "{{ url('users/ajax_listado') }}",
					type : 'GET',
					data: {filtrar_usuarios:filtrar_usuarios}
				},
				language: traduccion_datatable,
				dom: 'Bfrtip',
				buttons: [
					{"extend": 'pdf', "text":'Export',"className": 'btn btn-danger', "orientation": 'landscape', title: 'Usuarios'},
					{"extend": 'copy', "text":'Export',"className": 'btn btn-primary', title: 'Usuarios'},
					{"extend": 'excel', "text":'Export',"className": 'btn btn-success', title: 'Usuarios'},
					{"extend": 'print', "text":'Export',"className": 'btn btn-secondary', title: 'Usuarios'}
				],
				initComplete: function () {
					$('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
					$('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
					$('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
					$('.buttons-print').html('<span class="bi bi-printer" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir');
				}
			});


			$select_roles = $('#roles');
			$.ajax({
				url: "{{ url('roles/ajax_dropdown') }}"
				, "type": "POST"
				, data:{length:'',start:0}
				, dataType: 'JSON'
				, success: function (data) {
					//clear the current content of the select
					$select_roles.html('');
					//iterate over the data and append a select option
					$.each(data.roles, function (key, val) {
						$select_roles.append('<option value="' + val.id + '">' + val.nombre + '</option>');
					})
				}
				, error: function () {
					$select_roles.html('<option id="-1">Ninguna disponible</option>');
				}
			});

			$select_areas = $('#areas');
			$.ajax({
				url: "{{ url('area/ajax_dropdown') }}"
				, "type": "POST"
				, data:{length:'',start:0}
				, dataType: 'JSON'
				, success: function (data) {
					//clear the current content of the select
					$select_areas.html('');
					//iterate over the data and append a select option
					$.each(data.areas, function (key, val) {
						$select_areas.append('<option value="' + val.id + '">' + val.nombre + '</option>');
					})
				}
				, error: function () {
					$select_areas.html('<option id="-1">Ninguna disponible</option>');
				}
			});*/
		});

		$('input[type="checkbox"]').on('change', function() {
			// Guardo el nombre del checkbox que ha sido clickeado
			let variableName = $(this).attr('name');
			// Verifico si está checkeado
			let isChecked = $(this).is(':checked');
			// Obtengo el div correspondiente basado en el id del checkbox
			let targetDiv = $('#div_' + variableName);
			// Si está checkeado, muestro el formulario
			if (isChecked) {
				targetDiv.slideUp(); // Oculto el formulario
			} else {
				targetDiv.slideDown(); // Muestro el formulario
			}
			// Envío el AJAX para guardar el estado
			$.ajax({
				url: '{{ route("users.guardar_opciones") }}',
				type: 'POST',
				data: {
					_token: '{{ csrf_token() }}',
					[variableName]: isChecked ? 1 : 0
				},
				dataType: "JSON",
				success: function(response) {
					alert('Estado guardado correctamente');
					if (variableName == 'notificaciones_email_aleph') {
						window.location.href = '{{ route("configuracion.index") }}';
					} else {
						location.reload(); // Refresca la pantalla
					}
				},
				error: function(error) {
					alert('Error al guardar el estado', error);
				}
			});
		});

		function deshabilitar_usuario(id, temporal = false) {
			var texto = temporal 
				? "¿Está seguro de que desea deshabilitar el usuario de forma temporal?" 
				: "¿Está seguro de que desea deshabilitar el usuario?";
			if (temporal) {
				url = "{{ route('users.deshabilitar_usuario_temporal', ':id') }}";
			} else {
				url = "{{ route('users.deshabilitar_usuario', ':id') }}";
			}
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
						url: url.replace(':id', id),
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

	<div class="container-full-width" id="pagina-permisos">
		<div class="row">
			<div class="col-md-12">
				<h2>Usuarios</h2>
				<br>
				<div class="accordion" id="accordionOpcionUsuarios">
					<div class="accordion-item">
						<h2 class="accordion-header" id="headingOne">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
								data-bs-target="#opciones-usuarios" aria-expanded="true" aria-controls="collapseOne">
								Opciones
							</button>
						</h2>
						<div id="opciones-usuarios" class="accordion-collapse collapse" aria-labelledby="headingOne" 
							data-bs-parent="#accordionOpcionUsuarios">
							<div class="accordion-body">
								<form action="{{ route('users.guardar_opciones') }}" method="POST">
									@csrf
									<div class="row">
										<div class="col-md-12">
											<div class="mb-3 row">
												<label class="form-check-label col-md-3">Requerir cambio de contraseña después de 30 días</label>
												<div class="col-md-9">
													<input type="checkbox" value="1" <?php if($reset_password_30_dias){ echo 'checked'; }?> 
														name="reset_password_30_dias" id="reset_password_30_dias">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="mb-3 row">
												<label class="form-check-label col-md-3">Configurar contraseñas</label>
												<div class="col-md-3">
													<input type="checkbox" value="1" <?php if($configurar_claves){ echo 'checked'; }?> 
														name="configurar_claves" id="configurar_claves">
												</div>
											</div>
										</div>
									</div>
									<br>
									<button type="submit" class="btn btn-success"><span class="bi bi-save"></span> Guardar opciones</button>
								</form>
								<hr>
							</div>
						</div>
					</div>
				</div>
				
				<!--<div class="accordion" id="accordionFiltrosUsuarios">
					<div class="accordion-item">
						<h2 class="accordion-header" id="headingOne">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
								data-bs-target="#filtros-usuarios" aria-expanded="true" aria-controls="collapseOne">
								Filtros
							</button>
						</h2>
						<div id="filtros-usuarios" class="accordion-collapse collapse" aria-labelledby="headingOne" 
							data-bs-parent="#accordionFiltrosUsuarios">
							<div class="accordion-body">
								<div class="form-group">
									<label class="control-label">Filtro de usuarios</label>
									<select class="form-control" name="filtrar_usuarios" id="filtrar_usuarios" onchange="filtrar_usuarios()" style="width:200px;">
										<option value="0" selected="">Solo habilitados</option>
										<option value="1">Todos</option>
										<option value="2">Eliminados</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>-->

				<br>
				<div class="float-right">
					<a href="{{ route('users.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
						{{ __('Nuevo Usuario') }}
					</a>
				</div>
				<br>
				

				<!-- Tabla de usuarios -->
				<div class="p-12 sm:p-8 bg-white shadow sm:rounded-lg">
									<!-- Manejo de errores -->
					<div style="margin-top:1rem;">
						@if (session('error')) 
						<div class="alert alert-danger">
							{{ session('error') }}
						</div>
						@endif
						@if (session('success'))
						<div class="alert alert-success" role="alert">
								{{ session('success') }}
							</div>
						@endif
					</div>

					<div class="table-responsive">
						<div class="float-left">
							@if ($permiso_agregar_usuario)
							<a href="#" class="btn btn-outline-primary float-right" data-placement="left" style="border-radius:20px;!important;margin-right:5px; ">
								<i class="fas fa-file-import"></i> {{ __('Importar Usuarios') }}
							</a>
							@endif

							@if ($permiso_agregar_usuario)
							<a href="{{ route('users.create') }}" class="btn btn-outline-success float-right" data-placement="left" style="border-radius:20px;!important;margin-right:5px;">
								<i class="fas fa-plus"></i> {{ __('Agregar Usuario') }}
							</a>
							@endif

						</div>
						<table id="usuarios-table" class="cell-border" style="width:100%">
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
								<?php 
								$candadito = "";
									if ($user->habilitado != 1){ 
										$user->habilitado = 'No'; 
										$candadito = "<i class='fas fa-lock'></i>";
									} else { 
										$user->habilitado = 'Si';  
										$candadito = "<i class='fas fa-lock-open'></i>";
									}
								?>
									<tr>
										<td>{{ $user->nombre }}</td>
										<td>{{ $user->apellido }}</td>
										<td>{{ $user->username }}</td>
										<td>{{ $user->email }}</td>
										<td>{{ $user->nombre_rol }}</td>
										<td>{{ $user->habilitado }}</td>
										<td>{{ $user->bloqueado ? 'Sí' : 'No' }}</td>
										<td>
											@if ($permiso_editar_usuario)
											<a class="btn btn-sm btn-outline-primary" title="Editar" href="{{ route('users.edit', $user->user_id) }}">
												<i class="fas fa-pencil-alt"></i>
											</a>
											@endif
											
											@if ($permiso_deshabilitar_usuario)
											<a class="btn btn-sm btn-outline-warning" href="javascript:void(0)" title="Deshabilitar" onclick="deshabilitar_usuario('{{ $user->user_id}}',0)">
												<?php echo $candadito; ?>
											</a>
											@endif

											@if ($permiso_deshabilitar_usuario)
											<a class="btn btn-sm btn-outline-warning" href="javascript:void(0)" title="Deshabilitar temporalmente" onclick="deshabilitar_usuario('{{ $user->user_id}}',2)">
												<span class="fas fa-clock"></span>
											</a>
											@endif

											@if ($permiso_blanquear_password)
											<a class="btn btn-sm btn-outline-info" href="javascript:void(0)" title="Blanquear" onclick="blanquear_psw('{{ $user->user_id }}')">
												<i class="fas fa-key"></i>
											</a>
											@endif

											@if ($permiso_eliminar_usuario)
												<form action="{{ route('users.destroy', $user->user_id) }}" method="POST" style="display:inline;">
													@csrf
													@method('DELETE')
													<button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar este usuario?')">
														<i class="fas fa-trash"></i>
													</button>
												</form>
											@endif										</td>
												<!-- Botón para abrir el formulario de cambiar contraseña
												<a href="{{ route('password.change', $user->user_id) }}" class="btn btn-warning btn-sm">
													{{ __('Cambiar contraseña') }}
												</a> -->
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
	</div>

</x-app-layout>
