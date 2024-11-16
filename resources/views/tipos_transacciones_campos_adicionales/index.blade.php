<x-app-layout title="Campos adicionales" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Tipos de Transacciones') }}
		</h2>
	</x-slot>
	<!-- Primary Navigation Menu -->
	@php
	$user = Auth::user()->username;
	$email = Auth::user()->email;
	$permiso_agregar_roles = tiene_permiso('add_campo_adicional');
	$permiso_editar_roles = tiene_permiso('edit_rol');
	$permiso_eliminar_roles = tiene_permiso('del_rol');
	@endphp
	<?php #dd($tipos_campos); 
	?>

	<script type="text/javascript">
		var table;
		var save_method;

		jQuery(document).ready(function($) {
			table = $('#tipos_transacciones_table').DataTable({
				"ajax": {
					url: "{{ url('tipos_transacciones_campos_adicionales/ajax_listado') }}",
					type: 'GET'
				},
				language: traduccion_datatable,
				dom: 'Bfrtip',
				columnDefs: [{
					"targets": 'no-sort',
					"orderable": false
				}],
				buttons: [{
						"extend": 'pdf',
						"text": 'Export',
						"className": 'btn btn-danger',
						"orientation": 'landscape',
						title: 'Roles'
					},
					{
						"extend": 'copy',
						"text": 'Export',
						"className": 'btn btn-primary',
						title: 'Roles'
					},
					{
						"extend": 'excel',
						"text": 'Export',
						"className": 'btn btn-success',
						title: 'Roles'
					},
					{
						"extend": 'print',
						"text": 'Export',
						"className": 'btn btn-secondary',
						title: 'Roles'
					}
				],
				initComplete: function() {
					$('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
					$('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
					$('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
					$('.buttons-print').html('<span class="bi bi-printer" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir');
				}
			});
		});

		function reload_table() {
			table.ajax.reload(null, false);
		}

		function add_campo_adicional() {
			save_method = 'add';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			//$('#modal_form_campo_adicional').modal('show');
			$('#modal_form').modal('show');
			$('.modal-title').text('Agregar campos adicionales');
			$('#accion').val('add');
			$('#form').attr('action', "{{ url('tipos_transacciones_campos_adicionales') }}");
			$('#method').val('POST');
		}

		function edit_campos_adicionales(id) {

			//console.log('edit_campos_adicionales '+id);
			save_method = 'update';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');


			$.ajax({
				url: "{{ route('tipos_transacciones_campos_adicionales.ajax_edit', ':id') }}".replace(':id', id),
				type: "GET",
				dataType: "JSON",
				success: function(data) {
					$('[name="id"]').val(data.id);
					$('[name="nombre_campo"]').val(data.nombre_campo);
					$('[name="nombre_mostrar"]').val(data.nombre_mostrar);
					$('[name="visible"]').val(data.visible);
					$('[name="orden_listado"]').val(data.orden_listado);
					$('[name="requerido"]').val(data.requerido);
					$('[name="tipo"]').val(data.tipo);
					$('[name="valor_default"]').val(data.valor_default);


					$('#modal_form_campo_adicional').modal('show');
					$('.modal-title').text('Editar campo adicional de tipo de transacción');
					//$('#form').attr('action', "{{ url('tipos_transacciones_campos_adicionales') }}" + "/" + id);
					//$('#method').val('PUT');
				},
				error: function(jqXHR, textStatus, errorThrown) {
					if (jqXHR.status === 404) {
						alert("El registro no fue encontrado.");
					} else {
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				}
			});
		}

		function delete_rol(id) {
			if (confirm('¿Desea borrar el tipo de transacción?')) {

				$.ajax({
					url: "{{ url('tipos_transacciones/ajax_delete') }}" + "/" + id,
					type: "POST",
					dataType: "JSON",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						swal.fire("Aviso", "Tipo de transacción eliminado con éxito.", "success");

						$('#modal_form_campo_adicional').modal('hide');
						reload_table();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});

			}
		}


		function guardar_datos() {
			let form_data = $('#form').serializeArray();
			let url_guarda_datos = "{{ url('tipos_transacciones_campos_adicionales') }}";
			let type_guarda_datos = "POST";

			if ($('#accion').val() != "add") {
				url_guarda_datos = "{{ url('tipos_transacciones_campos_adicionales') }}" + "/" + $('[name="id"]').val();
				type_guarda_datos = "PUT";
			}

			show_loading();
			$.ajax({
				url: url_guarda_datos,
				type: type_guarda_datos,
				data: {
					form_data: form_data
				},
				dataType: "JSON",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(data) {
					hide_loading();
					if (data.status == 0) {
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
					} else {
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
				error: function(jqXHR, textStatus, errorThrown) {
					show_ajax_error_message(jqXHR, textStatus, errorThrown);
				}
			});
		}
	</script>
	<!--LISTADO-->
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h2>Campos Adicionales</h2>
				@include('layouts.partials.message')
				@if ($errors->any())
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
				@endif

				<div class="table-responsive">
					<div class="d-flex mb-2">
						<button id="agregar" class="btn btn-success mr-2" onclick="add_campo_adicional()">
							<i class="bi bi-plus"></i> {{ __('Nuevo Campo Adicional') }}
						</button>
					</div>
					<table id="tipos_transacciones_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<th>Nombre</th>
							<th>Nombre a Mostrar</th>
							<th>Es visible?</th>
							<th>Orden</th>
							<th>Es obligatorio?</th>
							<th>Tipo</th>
							<th>Default</th>
							<th style="width:20%;" class="no-sort">Acción</th>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>

			</div>
		</div>

	</div>

	<?php
	#dd($campos_adicionales);
	#dd($tipos_campos);
	?>
	<div class="modal fade" id="modal_form" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Formulario tipos de transacciones</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
					@csrf
					<input name="_method" type="hidden" id="method">
					<div class="modal-body form">
						<input type="hidden" value="" name="id" />
						<input name="accion" id="accion" class="form-control" type="hidden">
						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Nombre') }}</label>
								<div class="col-md-9">
									<input name="nombre_campo" minlength="3" maxlength="255" placeholder="Nombre del tipo de transacción"
										id="nombre_campo" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Nombre a Mostrar') }}</label>
								<div class="col-md-9">
									<input name="nombre_mostrar" minlength="3" maxlength="255" placeholder="Nombre a Mostrar"
										id="nombre_mostrar" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>

						<!-- en los siguientes controles checkbox, agrego un hidden con el mismo nombre para enviar 
									 valor "0" para que se envíe al server, cuando se setea el checkbox, se manda el valor de este
									 ya que el checkbox tiene prioridad sobre el hidden -->
						<div class="form-body">
							<div class="row mb-3">
								<label class="col-md-3 col-form-label">{{ __('Visibilidad en Formulario?') }}</label>
								<div class="col-md-9">
									<div class="form-check form-switch">
										<input type="hidden" name="visible" value="0">
										<input class="form-check-input" name="visible" id="visible"
											value="1" type="checkbox">
										<label class="form-check-label" for="visible"></label>
									</div>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Orden en el Formulario') }}</label>
								<div class="col-md-9">
									<input name="orden_abm" placeholder="1"
										id="orden_abm" class="form-control" type="number" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="row mb-3">
								<label class="col-md-3 col-form-label">{{ __('Es Obligatorio?') }}</label>
								<div class="col-md-9">
									<div class="form-check form-switch">
										<input type="hidden" name="requerido" value="0">
										<input class="form-check-input" name="requerido" id="requerido"
											value="1" type="checkbox">
										<label class="form-check-label" for="requerido"></label>
									</div>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Tipo de Campo') }}</label>
								<div class="col-md-9">
									<select id="tipo" name="tipo" class="mt-1 block w-full form-control" required>
										<option value="0">
											{{ __('Elija un Tipo de Campo') }}
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

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Valor por Defecto') }}</label>
								<div class="col-md-9">
									<input name="valor_default" minlength="3" maxlength="255" placeholder="Valor por defecto"
										id="valor_default" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Guardar</button>
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>


	<div class="modal fade" id="modal_form_campo_adicional" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Formulario campos adicionales</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<!--form id="form_campos_adicionales" method="POST" enctype="multipart/form-data" class="form-horizontal" action="{{ route('tipos_transacciones_campos_adicionales.store') }}"-->
				<form id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
					@csrf
					<input name="_method" type="hidden" id="method">
					<div class="modal-body form">
						<input type="hidden" value="" name="accion" id="accion" />
						<input type="hidden" value="" name="id" />
						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Nombre') }}</label>
								<div class="col-md-9">
									<input name="nombre_campo" minlength="3" maxlength="255" placeholder="Nombre del tipo de transacción"
										id="nombre_campo" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Nombre a Mostrar') }}</label>
								<div class="col-md-9">
									<input name="nombre_mostrar" minlength="3" maxlength="255" placeholder="Nombre a Mostrar"
										id="nombre_mostrar" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>


						<!-- en los siguientes controles checkbox, agrego un hidden con el mismo nombre para enviar 
									 valor "0" para que se envíe al server, cuando se setea el checkbox, se manda el valor de este
									 ya que el checkbox tiene prioridad sobre el hidden -->
						<div class="form-body">
							<div class="row mb-3">
								<label class="col-md-3 col-form-label">{{ __('Visibilidad en Formulario?') }}</label>
								<div class="col-md-9">
									<div class="form-check form-switch">
										<input type="hidden" name="visible" value="0">
										<input class="form-check-input" name="visible" id="visible"
											value="1" type="checkbox">
										<label class="form-check-label" for="visible"></label>
									</div>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Orden en el Formulario') }}</label>
								<div class="col-md-9">
									<input name="orden_abm" placeholder="1"
										id="orden_abm" class="form-control" type="number" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="row mb-3">
								<label class="col-md-3 col-form-label">{{ __('Es Obligatorio?') }}</label>
								<div class="col-md-9">
									<div class="form-check form-switch">
										<input type="hidden" name="requerido" value="0">
										<input class="form-check-input" name="requerido" id="requerido"
											value="1" type="checkbox">
										<label class="form-check-label" for="requerido"></label>
									</div>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Tipo de Campo') }}</label>
								<div class="col-md-9">
									<select id="tipo" name="tipo" class="mt-1 block w-full form-control" required>
										<option value="0">
											{{ __('Elija un Tipo de Campo') }}
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

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Valor por Defecto') }}</label>
								<div class="col-md-9">
									<input name="valor_default" minlength="3" maxlength="255" placeholder="Valor por defecto"
										id="valor_default" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>


					</div>
					<div class="modal-footer">
						<a onclick="guardar_datos()" class="btn btn-primary">{{ __('Guardar') }}</a>
						<!--a type="submit" class="btn btn-primary">{{ __('Guardar') }}</a-->
						<a class="btn btn-danger" data-bs-dismiss="modal">Cancelar</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-app-layout>