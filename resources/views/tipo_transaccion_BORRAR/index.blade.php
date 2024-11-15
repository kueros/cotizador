<x-app-layout title="Tipos de Transacciones" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Roles') }}
		</h2>
	</x-slot>
	<!-- Primary Navigation Menu -->
	@php
	$user = Auth::user()->username;
	$email = Auth::user()->email;
	$permiso_agregar_roles = tiene_permiso('add_tipo_transaccion');
	$permiso_editar_roles = tiene_permiso('edit_tipo_transaccion');
	$permiso_eliminar_roles = tiene_permiso('del_rol');
	@endphp

	<script type="text/javascript">
		var table;
		var save_method;

		jQuery(document).ready(function($) {
			table = $('#tipos-transacciones-table').DataTable({
				"ajax": {
					url: "{{ url('tipo_transaccion/ajax_listado') }}",
					type: 'GET'
				},
				language: traduccion_datatable,
				dom: 'Bfrtip',
				columnDefs: [{
					"targets": 'no-sort',
					"orderable": false
				}],
				buttons: [{
						"extend": 'copy',
						"text": 'Export',
						"className": 'btn btn-primary',
						title: 'Roles'
					},
					{
						"extend": 'pdf',
						"text": 'Export',
						"className": 'btn btn-danger',
						"orientation": 'landscape',
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
					$('.buttons-print').html(
						'<span class="bi bi-printer" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir'
					);
				}
			});
		});

		function reload_table() {
			table.ajax.reload(null, false);
		}

		function add_tipo_transaccion() {
			save_method = 'add';
			$('#form_tt')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#modal_form').modal('show');
			$('.modal-title').text('Agregar tipos de transacción');
			$('#accion').val('add');
			$('#form_tt').attr('action', "{{ url('tipo_transaccion') }}");
			$('#method').val('POST');
		}

		function edit_tipo_transaccion(id) {
			save_method = 'update';
			$('#form_tt')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');

			$.ajax({
				url: "{{ url('tipo_transaccion/ajax_edit/') }}" + "/" + id,
				type: "GET",
				dataType: "JSON",
				success: function(data) {
					$('[name="id"]').val(data.id);
					$('[name="nombre"]').val(data.nombre);

					<?php //if($utilizar_id_grupo){
					?>
					$('[name="id_grupo"]').val(data.id_grupo);
					<?php
					//}
					?>

					$('#modal_form').modal('show');
					$('.modal-title').text('Editar tipo de transacción');
					$('#form_tt').attr('action', "{{ url('tipo_transaccion') }}" + "/" + id);
					$('#method').val('PUT');
				},
				error: function(jqXHR, textStatus, errorThrown) {
					show_ajax_error_message(jqXHR, textStatus, errorThrown);
				}
			});
		}

		function delete_tipo_transaccion(id) {
			if (confirm('¿Desea borrar el tipo de transacción?')) {

				$.ajax({
					url: "{{ url('tipo_transaccion/ajax_delete') }}" + "/" + id,
					type: "POST",
					dataType: "JSON",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						swal.fire("Aviso", "Tipo de transacción eliminado con éxito.", "success");

						$('#modal_form').modal('hide');
						reload_table();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});

			}
		}

		function cambiar_tipo_campo(selector) {
			var tipo_campo = $(selector).val();

			switch (tipo_campo) {
				case '2':
					$('#div_valores_selector').show();
					$('#div_modelo_selector').hide();
					break;
				case '5':
					$('#div_valores_selector').hide();
					$('#div_modelo_selector').show();
					break;
				default:
					$('#div_valores_selector').hide();
					$('#div_modelo_selector').hide();
					break;
			}
		}

		function guardar_datos() {
			let form_data = $('#form_tt').serializeArray();
			let url_guarda_datos = "{{ url('tipo_transaccion') }}";
			let type_guarda_datos = "POST";

			if (!validar_campos_requeridos('form_tt')) {
				$('#form_tt')[0].reportValidity();
				//swal("Aviso", "Complete los campos obligatorios", "warning");
				return false;
			}

			if ($('#accion').val() != "add") {
				url_guarda_datos = "{{ url('tipo_transaccion') }}" + "/" + $('[name="id"]').val();
				type_guarda_datos = "PUT";
			}

			//show_loading();
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
				<h2>Tipos de Transacciones</h2>
				@include('layouts.partials.message')

				<div class="table-responsive">
					<div class="d-flex mb-2">
						<button id="agregar" class="btn btn-success mr-2" onclick="add_tipo_transaccion()">
							<i class="bi bi-plus"></i> {{ __('Nuevo Tipo de Transacción') }}
						</button>
						<a id="agregar_campos_adicionales" class="btn btn-success ml-auto"
							href="<?= route('tipo_transaccion.index_tipo_campo') ?>">
							<i class="bi bi-plus"></i> {{ __('Agregar Campos Adicionales') }}
						</a>
					</div>
					<table id="tipos-transacciones-table" class="table table-striped table-bordered" cellspacing="0"
						width="100%">
						<thead>
							<th>Nombre</th>
							<th>Descripción</th>
							<th style="width:20%;" class="no-sort">Acción</th>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="modal_form" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Formulario tipos de transacción</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<form id="form_tt" method="POST" enctype="multipart/form-data" class="form-horizontal" action="{{ route('tipo_transaccion.store') }}">
					@csrf
					<input name="_method" type="hidden" id="method">
					<div class="modal-body form">
						<input type="hidden" value="" name="id" />
						<input name="accion" id="accion" class="form-control" type="hidden">
						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">Nombre</label>
								<div class="col-md-9">
									<input name="nombre" maxlength="255" placeholder="Nombre del tipo de transacción"
										class="form-control" type="text">
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




</x-app-layout>