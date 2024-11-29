<x-app-layout title="Detalles de Alertas" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard'],['title' => 'Alertas', 'url' => '/alertasIndex']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Detalle del Alerta') }}
		</h2>
	</x-slot>
	<!-- Primary Navigation Menu -->
<!--	@php
 	$user = Auth::user()->username;
	$email = Auth::user()->email;
	$permiso_agregar_roles = tiene_permiso('add_alerta');
	$permiso_editar_roles = tiene_permiso('edit_rol');
	$permiso_eliminar_roles = tiene_permiso('del_rol');
@endphp
-->	
<?php
#dd($detalles_alertas);
?>
	<script type="text/javascript">
		alertas_id = <?php echo $id; ?>;
		var table;
		var save_method;

		jQuery(document).ready(function($) {
			table = $('#detalles_alertas_table').DataTable({
				"ajax": {
					url: "{{ url('alertas_detalles/ajax_listado') }}",
					type: 'GET',
					data: function(d) { // Agrega parámetros adicionales a la solicitud
						d.alertas_id = alertas_id;
        			}
				},
				language: traduccion_datatable,
				//dom: 'Bfrtip',
				columnDefs: [{
					"targets": 'no-sort',
					"orderable": false
				}],
				layout: {
					topStart: {
						buttons: [{
								"extend": 'pdf',
								"text": 'Export',
								"className": 'btn btn-danger',
								"orientation": 'landscape',
								title: 'Detalles Alertas'
							},
							{
								"extend": 'copy',
								"text": 'Export',
								"className": 'btn btn-primary',
								title: 'Detalles Alertas'
							},
							{
								"extend": 'excel',
								"text": 'Export',
								"className": 'btn btn-success',
								title: 'Detalles Alertas'
							},
							{
								"extend": 'print',
								"text": 'Export',
								"className": 'btn btn-secondary',
								title: 'Detalles Alertas'
							}
						]
					},
					bottomEnd: {
						paging: {
							firstLast: false  // Esto debería eliminar los botones "Primero" y "Último"
						}
					}
				},
				initComplete: function() {
					$('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
					$('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
					$('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
					$('.buttons-print').html('<span class="bi bi-printer" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir');
				}
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
			$('#modal_form_alertas_detalles').modal('show');
			$('.modal-title').text('Agregar alerta');
			$('#accion').val('add');
			$('#form').attr('action', "{{ url('alertas') }}");
			$('#method').val('POST');
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function edit_alertas_detalles(id) {
			save_method = 'update';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');
			$.ajax({
				url: "{{ route('alertas_detalles.ajax_edit', ':id') }}".replace(':id', id),
				type: "GET",
				dataType: "JSON",
				success: function(data) {
					$('[name="id"]').val(data.id);
					$('[name="funciones_id"]').val(data.funciones_id);
					$('[name="fecha_desde"]').val(data.fecha_desde);
					$('[name="fecha_hasta"]').val(data.fecha_hasta);
					$('#modal_form_alertas_detalles').modal('show');
					$('.modal-title').text('Editar Detalles de Alertas');
					//$('#form').attr('action', "{{ url('alertas_detalles') }}" + "/" + id);
					//$('#method').val('PUT');
				},
				error: function(jqXHR, textStatus, errorThrown) {
					show_ajax_error_message(jqXHR, textStatus, errorThrown);
				}
			});
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function delete_alertas_detalles(id) {
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

						$('#modal_form_alertas_detalles').modal('hide');
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
			let url_guarda_datos = "{{ url('alertas_detalles') }}";
			let type_guarda_datos = "POST";

			if ($('#accion').val() != "add") {
				url_guarda_datos = "{{ url('alertas_detalles') }}" + "/" + $('[name="id"]').val();
				type_guarda_datos = "PUT";
				console.log('asdf ' + $('[name="id"]').val());
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
				<h2>Detalle de la Alerta</h2>
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
					<table id="detalles_alertas_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<th>Función</th>
							<th>Fecha Desde</th>
							<th>Fecha Hasta</th>
							<!--th style="width:20%;" class="no-sort">Acción</th-->
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="modal_form_alertas_detalles" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Formulario alertas</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
					@csrf
					<input name="_method" type="hidden" id="method">
					<input name="alertas_id" id="alertas_id" class="form-control" type="hidden" value="<?php echo $id; ?>">
					<div class="modal-body form">
						<input type="hidden" value="" name="id" />
						<input name="accion" id="accion" class="form-control" type="hidden">
<?php 
#dd($funciones);
?>
						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Función') }}</label>
								<div class="col-md-9">
									<select id="funciones_id" name="funciones_id" class="mt-1 block w-full form-control" required>
										<option value="0">
											{{ __('Elija una Función') }}
										</option>
										@foreach($funciones as $funcion)
										<option value="{{ $funcion->id }}">
											{{ $funcion->nombre }}
										</option>
										@endforeach
									</select>
									<span class="help-block"></span>
								</div>
							</div>
						</div>


						<div class="mb-3 row">
							<label class="col-form-label col-md-3">Fecha Desde</label>
							<div class="col-md-9">
								<input 
									name="fecha_desde" 
									maxlength="255" 
									placeholder="Descripción del alerta" 
									class="form-control" 
									type="date" 
									required 
									pattern="^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$" 
									title="Solo se permiten letras, números, espacios, comas y puntos.">
								<span class="help-block"></span>
							</div>
						</div>

						<div class="mb-3 row">
							<label class="col-form-label col-md-3">Fecha Hasta</label>
							<div class="col-md-9">
								<input 
									name="fecha_hasta" 
									maxlength="255" 
									placeholder="Descripción del alerta" 
									class="form-control" 
									type="date" 
									required 
									pattern="^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$" 
									title="Solo se permiten letras, números, espacios, comas y puntos.">
								<span class="help-block"></span>
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