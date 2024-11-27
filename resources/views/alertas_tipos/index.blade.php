<x-app-layout title="Tipos de Alertas" :breadcrumbs="[['title' => 'Inicio', 'url' => '/dashboard'],['title' => 'Alertas', 'url' => '/alertasIndex']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Tipos de Transacciones') }}
		</h2>
	</x-slot>
	<!-- Primary Navigation Menu -->
	@php
	$user = Auth::user()->username;
	$email = Auth::user()->email;
	$permiso_agregar_roles = tiene_permiso('add_alertas_tipos');
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
					url: "{{ url('alertas_tipos/ajax_guardar_columna') }}",
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
			table = $('#alertas_tipos_table').DataTable({
				"ajax": {
					url: "{{ url('alertas_tipos/ajax_listado') }}",
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
						title: 'Tipos de Alertas'
					},
					{
						"extend": 'copy',
						"text": 'Export',
						"className": 'btn btn-primary',
						title: 'Tipos de Alertas'
					},
					{
						"extend": 'excel',
						"text": 'Export',
						"className": 'btn btn-success',
						title: 'Tipos de Alertas'
					},
					{
						"extend": 'print',
						"text": 'Export',
						"className": 'btn btn-secondary',
						title: 'Tipos de Alertas'
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
		function add_alertas_tipos() {
			save_method = 'add';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#modal_form_alerta_tipo').modal('show');
			//$('#modal_form').modal('show');
			$('.modal-title').text('Agregar Tipos de Alertas');
			$('#accion').val('add');
			$('#form').attr('action', "{{ url('alertas_tipos') }}");
			$('#method').val('POST');
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function edit_alertas_tipos(id) {
			save_method = 'update';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');

			$.ajax({
				url: "{{ route('alertas_tipos.ajax_edit', ':id') }}".replace(':id', id),
				type: "GET",
				dataType: "JSON",
				success: function(data) {
					// Asigna los valores al formulario
					$('#form [name="id"]').val(data.id);
					$('#form [name="nombre"]').val(data.nombre);
					$('#modal_form_alerta_tipo').modal('show');
					$('.modal-title').text('Editar Tipo de Alerta');
					$('#form').attr('action', "{{ url('alertas_tipos') }}" + "/" + id);
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
		function delete_alertas_tipos(id) {
			if (confirm('¿Desea borrar este tipo de alerta?')) {

				$.ajax({
					url: "{{ route('alertas_tipos.ajax_delete', ':id') }}".replace(':id', id),
					type: "POST",
					dataType: "JSON",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						swal.fire("Aviso", "Tipo de Alerta eliminado con éxito.", "success");

						$('#modal_form_alerta_tipo').modal('hide');
						reload_table();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});

			}
		}

	</script>
	<!--LISTADO-->
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h2>Tipos de Alertas</h2>
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
						<button id="agregar" class="btn btn-success mr-2" onclick="add_alertas_tipos()">
							<i class="bi bi-plus"></i> {{ __('Agregar Tipo de Alerta') }}
						</button>
					</div>

					<table id="alertas_tipos_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<th>Nombre</th>
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

	<div class="modal fade" id="modal_form_alerta_tipo" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Formulario Tipos de Alerta</h5>
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
									<input name="nombre" minlength="3" maxlength="255" placeholder="Nombre del Tipo de Alerta"
										id="nombre" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
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