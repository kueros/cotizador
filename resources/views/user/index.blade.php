<x-app-layout title="Usuarios" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
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
	@endphp
	<script type="text/javascript">
		

		jQuery(document).ready(function($){
			$("#importador_form_usuarios").submit(function(e) {
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
			});
		});

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

	<div class="container-full-width">
		<div class="row">
			<div class="col-md-12">
				<h2>Usuarios</h2>

				<div class="accordion" id="accordionOpcionUsuarios">
					<div class="accordion-item">
						<h2 class="accordion-header" id="headingOne">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
								data-bs-target="#opciones-usuario" aria-expanded="true" aria-controls="collapseOne">
								Opciones
							</button>
						</h2>
						<div id="opciones-usuario" class="accordion-collapse collapse" aria-labelledby="headingOne" 
							data-bs-parent="#accordionOpcionUsuarios">
							<div class="accordion-body">
								<form action="{{ url('usuarios/opciones_submit') }}" method="post">
									<div class="row">
										<div class="form-body">
											<div class="form-group">
												<label class="control-label col-md-3">Tiempo de sesión (segundos)</label>
												<div class="col-md-3">
													<input class="form-control" name="session_time" type="number" min="60" max="864000" step="1" value=""/>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="form-body">
											<div class="form-group">
												<label class="control-label col-md-3">Habilitar múltiples roles</label>
												<div class="col-md-3">
													<input type="checkbox" value="1" name="usuarios_habilita_multiples_roles" id="usuarios_habilita_multiples_roles">
												</div>
											</div>
										</div>
									</div>
									@if (session('success'))
										<div class="alert alert-success" role="alert">
											{{ session('success') }}
										</div>
									@endif
								
									<br>
									<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar opciones</button>
								</form>
								<hr>
							</div>
						</div>
					</div>
				</div>
				<button id="agregar" class="btn btn-success" onclick="add_usuario()"><i class="bi bi-plus"></i> Agregar usuario</button>
				<button id="importar" class="btn btn-primary" onclick="importar_usuarios()"><i class="bi bi-import"></i> Importar usuarios</button>
				<a class="btn btn-info" href="{{ url('usuarios/campos_adicionales') }}"><i class="bi bi-th-list"></i> Campos adicionales</a>
				<br>
				<br>
				<div class="form-group">
					<label class="control-label">Filtro de usuarios</label>
					<select class="form-control" name="filtrar_usuarios" id="filtrar_usuarios" onchange="filtrar_usuarios()" style="width:200px;">
						<option value="0">Solo habilitados</option>
						<option value="1">Todos</option>
						<option value="2">Eliminados</option>
					</select>
				</div>
				<br>
				
				<div class="table-responsive">
					<table id="usuarios-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
						<?php
						if(!empty($campos_usuarios)){
							foreach($campos_usuarios as $campo){
								if($campo->nombre_campo == 'password'){
									continue;
								}
								if($campo->visible){
									echo '<th>'.$campo->nombre_mostrar.'</th>';
								}                            
							}
						}
						?>
						<th style="width:125px;">Acción</th>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
					
			</div>
		</div>
	</div>

</x-app-layout>
