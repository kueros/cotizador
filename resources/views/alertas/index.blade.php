<x-app-layout title="Alertas" :breadcrumbs="[['title' => 'Inicio', 'url' => '/dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Tipos de Transacciones') }}
		</h2>
	</x-slot>
	<!-- Primary Navigation Menu -->
	@php
	$user = Auth::user()->username;
	$email = Auth::user()->email;
	$permiso_agregar_roles = tiene_permiso('add_alerta');
	$permiso_editar_roles = tiene_permiso('edit_rol');
	$permiso_eliminar_roles = tiene_permiso('del_rol');
	@endphp
	<?php
	#dd($tipos_alertas); 
	?>

	<script type="text/javascript">
		var table;
		var save_method;

		jQuery(document).ready(function($) {

			/*******************************************************************************************************************************
			*******************************************************************************************************************************/
			$("#form").submit(function(e) {
				e.preventDefault();
				var formData = new FormData(this);

				//show_loading();
				$.ajax({
					url: "{{ url('alertas/ajax_guardar_columna') }}",
					type: "POST",
					data: formData,
					method: 'POST',
					cache: false,
					contentType: false,
					processData: false,
					success: function(data) {
						//hide_loading();
						console.log(data)
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
					}					},
					error: function(jqXHR) {
						hide_loading();
						var mensaje = "Ocurrió un error al guardar la columna.";
						if (jqXHR.responseText) {
							mensaje = jqXHR.responseText;
						}
						if (mensaje != "") {
							swal("Aviso", mensaje, "warning");
						}
					}
				});
			});

			/*******************************************************************************************************************************
			 *******************************************************************************************************************************/
			//tipo_transaccion_id = <?php #echo $id; ?>;
			table = $('#alertas_table').DataTable({
				"ajax": {
					url: "{{ url('alertas/ajax_listado') }}",
					type: 'GET',
/* 					data: function(d) { // Agrega parámetros adicionales a la solicitud
						d.tipo_transaccion_id = tipo_transaccion_id;
        			}
 */				},
				language: traduccion_datatable,
				dom: 'Bfrtip',
				columnDefs: [{
					"targets": 'no-sort',
					"orderable": true
				}],
				buttons: [{
						"extend": 'pdf',
						"text": 'Export',
						"className": 'btn btn-danger',
						"orientation": 'landscape',
						title: 'Alertas'
					},
					{
						"extend": 'copy',
						"text": 'Export',
						"className": 'btn btn-primary',
						title: 'Alertas'
					},
					{
						"extend": 'excel',
						"text": 'Export',
						"className": 'btn btn-success',
						title: 'Alertas'
					},
					{
						"extend": 'print',
						"text": 'Export',
						"className": 'btn btn-secondary',
						title: 'Alertas'
					}
				],
				initComplete: function() {
					$('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
					$('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
					$('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
					$('.buttons-print').html('<span class="bi bi-printer" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir');
				},
				"order": [[2, 'asc']]
			});
		});

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function reload_table() {
			table.ajax.reload(null, false);
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function add_alerta() {
			save_method = 'add';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#modal_form_alertas').modal('show');
			//$('#modal_form').modal('show');
			$('.modal-title').text('Agregar alertas');
			$('#accion').val('add');
			$('#form').attr('action', "{{ url('alertas') }}");
			$('#method').val('POST');
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function edit_alertas(id) {
			save_method = 'update';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');
			$.ajax({
				url: "{{ route('alertas.ajax_edit', ':id') }}".replace(':id', id),
				type: "GET",
				dataType: "JSON",
				success: function(data) {
					$('[name="id"]').val(data.id);
					$('[name="nombre"]').val(data.nombre);
					$('[name="descripcion"]').val(data.descripcion);
					$('[name="tipos_alertas_id"]').val(data.tipos_alertas_id);
					$('#modal_form_alertas').modal('show');
					$('.modal-title').text('Editar Alerta');
					$('#form').attr('action', "{{ url('alertas') }}" + "/" + id);
					$('#method').val('PUT');
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

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function delete_campos_adicionales(id) {
			if (confirm('¿Desea borrar el campo adicional de este tipo de transacción?')) {

				$.ajax({
					url: "{{ route('tipos_transacciones_campos_adicionales.ajax_delete', ':id') }}".replace(':id', id),
					type: "POST",
					dataType: "JSON",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						swal.fire("Aviso", "Campo adicional de este tipo de transacción eliminado con éxito.", "success");

						$('#modal_form_alertas').modal('hide');
						reload_table();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});

			}
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function guardar_datos() {
			let form_data = $('#form').serializeArray();
			let url_guarda_datos = "{{ url('alertas') }}";
			let type_guarda_datos = "POST";

			if ($('#accion').val() != "add") {
				url_guarda_datos = "{{ url('alertas') }}" + "/" + $('[name="id"]').val();
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

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function agregar_valor_selector() {
			var td = '<tr><td><input class="form-control" type="text" maxlength="255" minlength="1" required name="valores[]"/></td><td><a class="btn btn-danger" onclick="eliminar_valor(this)"><i class="bi bi-trash"></i></td></tr>';
			$('#valores_selector tbody').append(td);
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function eliminar_valor(button) {
			// Obtener la fila <tr> que contiene el botón de eliminar
			var row = button.closest('tr');
			
			// Eliminar la fila
			row.remove();
		}
	</script>
	<!--LISTADO-->
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h2>Alertas</h2>
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
<?php
#dd($alertas);
?>
				<div class="table-responsive">
					<div class="d-flex mb-2">
						<button id="agregar" class="btn btn-success mr-2" onclick="add_alerta()">
							<i class="bi bi-plus"></i> {{ __('Agregar Alerta') }}
						</button>
						<a class="btn btn-primary" href="{{ route('alertas_tipos') }}" title="Detalle Alerta">{{ __('Administrar Tipos de Alertas') }}</a>
					</div>

					<table id="alertas_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<th>Nombre</th>
							<th>Descripción</th>
							<th>Tipo de alerta</th>
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
		#dd($id);
		#dd($campos_adicionales);
		#dd($ultima_posicion);
	?>
	<?php
	?>

	<div class="modal fade" id="modal_form_alertas" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Formulario alertas</h5>
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
									<input name="nombre" minlength="3" maxlength="255" placeholder="Nombre de la alerta"
										id="nombre" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Descripcion') }}</label>
								<div class="col-md-9">
									<input name="descripcion" minlength="3" maxlength="255" placeholder="Descripción"
										id="descripcion" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>
<?php 
#dd($tipos_alertas);
dd($alertas);
?>
						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Tipo de Alerta') }}</label>
								<div class="col-md-9">
									<select id="tipos_alertas_id" name="tipos_alertas_id" class="mt-1 block w-full form-control" required>
										<option value="0">
											{{ __('Elija un Tipo de Alerta') }}
										</option>
										@foreach($tipos_alertas as $tipo_alerta)
										<option value="{{ $tipo_alerta->id }}">
											{{ $tipo_alerta->nombre }}
										</option>
										@endforeach
									</select>
									<span class="help-block"></span>
								</div>
							</div>
						</div>

						<div id="div_valores_selector" class="form-group" style="display:block">
							<hr>
							<a class="btn btn-success" onclick="agregar_valor_selector()">Agregar valor</a>
							<div class="table-responsive">
								<table class="table" id="valores_selector">
									<thead>
										<th>Valor</th>
										<th>Acción</th>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary" id="guardar_campo">Guardar</button>
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</x-app-layout>