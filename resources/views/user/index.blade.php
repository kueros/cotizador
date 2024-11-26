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
			language: traduccion_datatable,
            dom: 'Bfrtip',
			columnDefs: [
				{
					"targets": 'no-sort',
					"orderable": false
				}
			],
			aoColumns : [
				{ sWidth: '13%' },
				{ sWidth: '13%' },
				{ sWidth: '13%' },
				{ sWidth: '13%' },
				{ sWidth: '13%' },
				{ sWidth: '10%' },
				{ sWidth: '10%' },
				{ sWidth: '15%' }
			],
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
                $('.buttons-print').html('<span class="glyphicon glyphicon-print" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir');
            }
			});
		});

		function limpiar_campos_requeridos(form_id){
			$($('#'+form_id).prop('elements')).each(function(){
				if($(this).prop("required") && !$(this).prop("disabled")){
					$(this).removeClass('field-required');
				}
			});
		}

		function add_usuario()
		{
			save_method = 'add';
			limpiar_campos_requeridos('form');
			//$('#tabla_roles tbody').html('');
			$('#form_usuario')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			//roles_usuario = new Array();
			$('.modal-title').text('Agregar usuario');
			$('#accion').val('add');
			$('#password').prop("required",true);
			$('#repassword').prop("required",true);
			$('#modal_form').modal('show');
			//$('#form').attr('action', "{{ url('users') }}");
    		//$('#method').val('POST');
		}

		function edit_usuario(id)
		{
			save_method = 'update';
			limpiar_campos_requeridos('form');
			$('#form_usuario')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');
			//$('#tabla_roles tbody').html('');
			$('#password').prop("required",false);
			$('#repassword').prop("required",false);
			//roles_usuario = new Array();

			$.ajax({
				url : "{{ url('users/ajax_edit/') }}" + "/" + id,
				type: "GET",
				dataType: "JSON",
				success: function(data)
				{
					let user = data.user;
					$('[name="id"]').val(user.user_id);
					$('[name="nombre"]').val(user.nombre);
					$('[name="apellido"]').val(user.apellido);
					$('[name="username"]').val(user.username);
					$('[name="email"]').val(user.email);
					//$('[name="habilitado"]').val(user.habilitado);
					$('[name="habilitado"]').prop('checked', user.habilitado == 1);
					//$('[name="bloqueado"]').val(user.bloqueado);
					$('[name="bloqueado"]').prop('checked', user.bloqueado == 1);
					$('[name="rol_id"]').val(user.rol_id);

					//$('#form_usuario').attr('action', "{{ url('users') }}" + "/" + id);
            		//$('#method').val('PUT');

					/*data.roles_asignados.forEach(function(rol){
						roles_usuario.push(rol.rol_id);

						row_roles = '';
						row_roles += '<tr id="tr_rol_'+rol.rol_id+'">';
						row_roles += '<td>'+rol.rol_nombre+'</td>';
						row_roles += '<td><a class="btn btn-danger" onclick="eliminar_rol_usuario('+rol.rol_id+')"><span class="glyphicon glyphicon-trash"></span></a></td>';
						row_roles += '</tr>';

						$('#tabla_roles tbody').append(row_roles);
					});*/

					$('#modal_form').modal('show');
					$('.modal-title').text('Editar usuario');
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					show_ajax_error_message(jqXHR, textStatus, errorThrown);
				}
			});
		}

		function deshabilitar_usuario(id, temporal = false, habilitado) {
			console.log(habilitado)
			if(habilitado == 0){
				var texto = temporal 
					? "¿Está seguro de que desea habilitar el usuario de forma temporal?" 
					: "¿Está seguro de que desea habilitar el usuario?";
				var cartelito = "Usuario habilitado con éxito."
			} else {
				var texto = temporal 
					? "¿Está seguro de que desea deshabilitar el usuario de forma temporal?" 
					: "¿Está seguro de que desea deshabilitar el usuario?";
				var cartelito = "Usuario deshabilitado con éxito."
			}
 				if (temporal) {
				url = "{{ route('users.deshabilitar_usuario_temporal', ':id') }}";
			} else {
				url = "{{ route('users.deshabilitar_usuario', ':id') }}";
			}
			swal.fire({
				title: texto,
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: "OK",
				cancelButtonText: "Cancelar"
			}).then((result) => {
				if( result.isConfirmed ){
					show_loading(); // Función personalizada que muestra un loader
					$.ajax({ headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},
						url: url.replace(':id', id),
						type: "PATCH",
						data: {temporal: temporal},
						dataType: "JSON",
						success: function(data) {
							swal.fire({
								title: "Aviso",
								text: cartelito,
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
			swal.fire({
				title: "¿Desea blanquear la contraseña del usuario?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: "OK",
				cancelButtonText: "Cancelar"
			}).then((result) => {
				if( result.isConfirmed ){
					show_loading();
					$.ajax({ 
						headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},
						url: "{{ route('users.blanquear_password', ':id') }}".replace(':id', id),
						type: "PATCH",
						dataType: "JSON",
						success: function(data) {
							swal.fire({
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

		function delete_usuario(id)
		{
			swal.fire({
				title: "¿Desea borrar el usuario?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: "OK",
				cancelButtonText: "Cancelar"
			}).then((result) => {
				if( result.isConfirmed ){
					show_loading();
					$.ajax({
						url : "{{ url('/users/ajax_delete/') }}"+"/"+id,
						type: "POST",
						dataType: "JSON",
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						success: function(data)
						{
							swal.fire({
								title: "Aviso",
								text: "Usuario eliminado con éxito.",
								icon: "success"
							}).then(() => {
								// Recargar la tabla DataTables al cerrar el modal de éxito
								location.reload();
							});

							$('#modal_form').modal('hide');
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							show_ajax_error_message(jqXHR, textStatus, errorThrown);
						}
					});
				}
			});

		}

		function validar_campos_requeridos(form_id){
			var form_status = true;

			$($('#'+form_id).find(':input')).each(function(){
				if($(this).prop("required") && !$(this).prop("disabled")){
					if($(this).val()){
						if(!$(this)[0].checkValidity()){
							$(this).addClass('field-required');
							form_status = false;
						}else{
							$(this).removeClass('field-required');
						}
					}else{
						$(this).addClass('field-required');
						form_status = false;
					}
				}
			});

			return form_status;
		}
		
		function guardar_datos(){
			let form_data = $('#form_usuario').serializeArray();
			let url_guarda_datos = "{{ url('users') }}";
			let type_guarda_datos = "POST";

			if(!validar_campos_requeridos('form_usuario')){
				$('#form_usuario')[0].reportValidity();
				//swal("Aviso", "Complete los campos obligatorios", "warning");
				return false;
			}

			if( $('#accion').val() != "add" ){
				url_guarda_datos = "{{ url('users') }}" + "/" + $('[name="id"]').val();
				type_guarda_datos = "PUT";
			}

			show_loading();
			$.ajax({
				url : url_guarda_datos,
				type: type_guarda_datos,
				data: {form_data:form_data},
				dataType: "JSON",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(data){
					hide_loading();
					if(data.status == 0){
						let errorMessage = data.message + "</br>";
    					if (data.errors && Object.keys(data.errors).length > 0) {
							// Recorre cada campo y sus mensajes de error
							for (let field in data.errors) {
								if (data.errors.hasOwnProperty(field)) {
									errorMessage += `${field}: ${data.errors[field].join(", ")}</br>`;
								}
							}
						} else {
							errorMessage += "No se encontraron errores específicos para los campos.";
						}

						swal.fire("Aviso", errorMessage, "warning");
						return false;
					}else{
						swal.fire({
							title: "Aviso",
							text: data.message,
							icon: "success"
						}).then(() => {
							// Recargar la tabla DataTables al cerrar el modal de éxito
							location.reload();
						});
					}
				},
				error: function (jqXHR, textStatus, errorThrown){
					show_ajax_error_message(jqXHR, textStatus, errorThrown);
				}
			});
		}

	</script>

	<div class="container" id="pagina-permisos">
		<div class="row">
			<div class="col-md-12">
				<h2>Usuarios</h2>
				<br>
				@include('layouts.partials.message')
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
												<label class="form-check-label col-md-6">Requerir cambio de contraseña después de 30 días</label>
												<div class="col-md-6">
													<input type="checkbox" value="1" <?php if($reset_password_30_dias){ echo 'checked'; }?> 
														name="reset_password_30_dias" id="reset_password_30_dias">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="mb-3 row">
												<label class="form-check-label col-md-6">Configurar contraseñas</label>
												<div class="col-md-6">
													<input type="checkbox" value="1" <?php if($configurar_claves){ echo 'checked'; }?> 
														name="configurar_claves" id="configurar_claves">
												</div>
											</div>
										</div>
									</div>
									<br>
									<button type="submit" class="btn btn-success"><span class="bi bi-floppy2-fill"></span> Guardar opciones</button>
								</form>
								<hr>
							</div>
						</div>
					</div>
				</div>
				<br>

				
				<div class="table-responsive">
					<div class="float-left">
						@if ($permiso_agregar_usuario)
						<button id="agregar" class="btn btn-success float-right" onclick="add_usuario()">
							<i class="bi bi-plus"></i> {{ __('Agregar Usuario') }}
						</button>
						@endif
						@if ($permiso_agregar_usuario)
						<a href="#" class="btn btn-primary float-right" data-placement="left" style="border-radius:20px;!important;margin-right:5px; ">
							<i class="fas fa-file-import"></i> {{ __('Importar Usuarios') }}
						</a>
						@endif
					</div>
					<br>
					<table id="usuarios-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Nombre</th>
								<th>Apellido</th>
								<th>Username</th>
								<th>Email</th>
								<th>Rol</th>
								<th>Habilitado</th>
								<th>Bloqueado</th>
								<th class="text-center no-sort">Acciones</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($users as $user)
							<?php 
							$candadito = "";
								if ($user->habilitado != 1){ 
									$color = "btn-outline-info";
									$user_habilitado = 'No'; 
									$habilitado = 0;
									$candadito = "<i class='fas fa-lock'></i>";
									$titulo = "Habilitar";
								} else { 
									$color = "btn-outline-warning";
									$user_habilitado = 'Si';  
									$habilitado = 1;
									$candadito = "<i class='fas fa-lock-open'></i>";
									$titulo = "Deshabilitar";
								}
							?>
								<tr>
									<td>{{ $user->nombre }}</td>
									<td>{{ $user->apellido }}</td>
									<td>{{ $user->username }}</td>
									<td>{{ $user->email }}</td>
									<td>{{ $user->nombre_rol }}</td>
									<td>{{ $user_habilitado }}</td>
									<td>{{ $user->bloqueado ? 'Sí' : 'No' }}</td>
									<td>
										@if ($permiso_editar_usuario)
										<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" 
											onclick="edit_usuario('{{ $user->user_id }}')"><i class="bi bi-pencil-fill"></i>
										</a>
										@endif
										
										@if ($permiso_deshabilitar_usuario)
										<a class="btn btn-sm <?php echo $color; ?>" href="javascript:void(0)" 
											title="<?php echo $titulo; ?>" 
											onclick="deshabilitar_usuario('{{ $user->user_id}}',false,'{{ $habilitado }}')">
											<?php echo $candadito; ?>
										</a>
										@endif

										@if ($permiso_deshabilitar_usuario)
											@if ($habilitado)
												<a class="btn btn-sm btn-outline-warning" href="javascript:void(0)" 
													title="Deshabilitar temporalmente" 
													onclick="deshabilitar_usuario('{{ $user->user_id}}',true,'{{ $habilitado }}')">
													<span class="fas fa-clock"></span>
												</a>
											@endif
										@endif

										@if ($permiso_blanquear_password)
										<a class="btn btn-sm btn-outline-info" href="javascript:void(0)" title="Blanquear" onclick="blanquear_psw('{{ $user->user_id }}')">
											<i class="fas fa-key"></i>
										</a>
										@endif

										@if ($permiso_eliminar_usuario)
										<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" 
											onclick="delete_usuario('{{ $user->user_id }}')"><i class="bi bi-trash"></i>
										</a>
										@endif		
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>



				<div class="modal fade" id="modal_form" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Formulario de usuario</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<form id="form_usuario" method="post" action="" class="mt-6 space-y-6">
								<input name="_method" type="hidden" id="method">
								<input type="hidden" value="" name="accion" id="accion"/>
								<input type="hidden" value="" name="id"/>
								<div class="modal-body form">
									@csrf
									<div class="form-body">
										<div class="mb-3 row">
											<label class="col-form-label col-md-3">{{ __('Nombre de Usuario') }}</label>
											<div class="col-md-9">
												<input name="username" minlength="3" maxlength="255" placeholder="Nombre de Usuario" 
													id="username" class="form-control" type="text" required>
												<span class="help-block"></span>
											</div>
										</div>
									</div>

									<div class="form-body">
										<div class="mb-3 row">
											<label class="col-form-label col-md-3">{{ __('Nombre') }}</label>
											<div class="col-md-9">
												<input name="nombre" minlength="3" maxlength="255" placeholder="Nombre" 
													id="nombre" class="form-control" type="text" required>
												<span class="help-block"></span>
											</div>
										</div>
									</div>

									<div class="form-body">
										<div class="mb-3 row">
											<label class="col-form-label col-md-3">{{ __('Apellido') }}</label>
											<div class="col-md-9">
												<input name="apellido" minlength="3" maxlength="255" placeholder="Apellido" 
													id="apellido" class="form-control" type="text" required>
												<span class="help-block"></span>
											</div>
										</div>
									</div>

									<div class="form-body">
										<div class="mb-3 row">
											<label class="col-form-label col-md-3">{{ __('Email') }}</label>
											<div class="col-md-9">
												<input name="email" minlength="3" maxlength="255" placeholder="Email" 
													id="email" class="form-control" type="email" required>
												<span class="help-block"></span>
											</div>
										</div>
									</div>

									<div class="form-body">
										<div class="mb-3 row">
											<label class="col-form-label col-md-3">{{ __('Rol') }}</label>
											<div class="col-md-9">
												<select id="rol_id" name="rol_id" class="mt-1 block w-full form-control" required>
													<option value="" {{ old('rol_id', $user->rol_id) === null ? 'selected' : '' }}>
														{{ __('Elija un Rol') }}
													</option>
													@foreach($roles as $rol)
													<option value="{{ $rol->rol_id }}">
														{{ $rol->nombre }}
													</option>
													@endforeach
												</select>
												<span class="help-block"></span>
											</div>
										</div>
									</div>
									
									<!-- en los siguientes controles checkbox, agrego un hidden con el mismo nombre para enviar 
									 valor "0" para que se envíe al server, cuando se setea el checkbox, se manda el valor de este
									 ya que el checkbox tiene prioridad sobre el hidden -->
									<div class="form-body">
										<div class="row mb-3">
											<label class="col-md-3 col-form-label">{{ __('Habilitado') }}</label>
											<div class="col-md-9">
												<div class="form-check form-switch">
													<input type="hidden" name="habilitado" value="0">
													<input class="form-check-input" name="habilitado" id="habilitado" 
														value="1" type="checkbox" {{ old('habilitado', $user->habilitado) === null || old('habilitado', $user->habilitado) == 1 ? 'checked' : '' }} >
													<label class="form-check-label" for="habilitado"></label>
												</div>
											</div>
										</div>
									</div>

									<div class="form-body">
										<div class="row mb-3">
											<label class="col-md-3 col-form-label">{{ __('Bloqueado') }}</label>
											<div class="col-md-9">
												<div class="form-check form-switch">
													<input type="hidden" name="bloqueado" value="0">
													<input class="form-check-input" name="bloqueado" id="bloqueado" 
														value="1" type="checkbox" {{ old('bloqueado', $user->bloqueado) === null || old('bloqueado', $user->bloqueado) == 1 ? 'checked' : '' }} >
													<label class="form-check-label" for="bloqueado"></label>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<a onclick="guardar_datos()" class="btn btn-primary">{{ __('Guardar') }}</a>
									<a class="btn btn-danger" data-bs-dismiss="modal">Cancelar</a>
								</div>
							</form>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>

</x-app-layout>
