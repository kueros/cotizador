<x-app-layout title="ABM Tipos de Transacciones" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Tipos de Transacciones') }}
		</h2>
	</x-slot>
	<!-- Primary Navigation Menu -->
	@php
	$user = Auth::user()->username;
	$email = Auth::user()->email;
	$permiso_agregar_roles = tiene_permiso('add_tipo_transaccion');
	$permiso_editar_roles = tiene_permiso('edit_rol');
	$permiso_eliminar_roles = tiene_permiso('del_rol');
	@endphp

	<script type="text/javascript">
		var table;
		var save_method;

		jQuery(document).ready(function($) {
			table = $('#tipos_transacciones_table').DataTable({
				"ajax": {
					url: "{{ url('tipos_transacciones/ajax_listado') }}",
					type: 'GET',
				},
				language: traduccion_datatable,
				//dom: 'Bfrtip', // Habilitar los botones de exportación
				layout: {
					topStart: {
						buttons: [
							{
								"extend": 'pdf',
								"text": '<i class="fas fa-file-pdf"></i> PDF',
								"className": 'btn btn-danger',
								"orientation": 'landscape',
								title: 'Alertas',
								exportOptions: {
									columns: [0, 1, 2, 3] // Índices de las columnas que deseas incluir en la exportación
								}
							},
							{
								"extend": 'copy',
								"text": '<i class="fas fa-copy"></i> Copiar',
								"className": 'btn btn-primary',
								title: 'Alertas',
								exportOptions: {
									columns: ':visible' // Solo las columnas visibles
								}
							},
							{
								"extend": 'excel',
								"text": '<i class="fas fa-file-excel"></i> Excel',
								"className": 'btn btn-success',
								title: 'Alertas',
								exportOptions: {
									columns: [0, 1, 2, 3] // Índices de las columnas específicas
								}
							},
							{
								"extend": 'print',
								"text": '<i class="bi bi-printer"></i> Imprimir',
								"className": 'btn btn-secondary',
								title: 'Alertas',
								exportOptions: {
									columns: ':visible' // Exportar solo las columnas visibles
								}
							}
						]
					},
					bottomEnd: {
						paging: {
							firstLast: false  // Esto debería eliminar los botones "Primero" y "Último"
						}
					}
				},
				columnDefs: [{
					"targets": 'no-sort',
					"orderable": true
				}],
				//pagingType: 'simple_numbers',
				initComplete: function () {
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
		function add_tipo_transaccion() {
			save_method = 'add';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('.is-invalid').removeClass('is-invalid'); // Limpia las clases de error
			$('#modal_form').modal('show');
			$('.modal-title').text('Agregar tipo de transacción');
			$('#accion').val('add');
			$('#form').attr('action', "{{ url('tipos_transacciones') }}");
			$('#method').val('POST');
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function edit_tipos_transacciones(id) {
			save_method = 'update';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');
			$.ajax({
				url: "{{ url('tipos_transacciones/ajax_edit/') }}" + "/" + id,
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
					$('#form').attr('action', "{{ url('tipos_transacciones') }}" + "/" + id);
					$('#method').val('PATCH');
				},
				error: function(jqXHR, textStatus, errorThrown) {
					show_ajax_error_message(jqXHR, textStatus, errorThrown);
				}
			});
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function delete_tipos_transacciones(id) {
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

						$('#modal_form').modal('hide');
						reload_table();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});

			}
		}

		$('#form').submit(function(event) {
			event.preventDefault(); // Prevenir la acción por defecto del formulario

			let formData = $(this).serialize();
			let actionUrl = $(this).attr('action');
			let method = $('#method').val();

			$.ajax({
				url: actionUrl,
				type: method,
				data: formData,
				success: function(response) {
					if (response.status === 0) {
						// Mostrar errores de validación
						$.each(response.errors, function(key, value) {
							let input = $('[name="' + key + '"]');
							input.addClass('is-invalid');
							input.siblings('.help-block').text(value[0]);
						});
					} else {
						// Procesar éxito
						$('#modal_form').modal('hide');
						reload_table();
						Swal.fire('Éxito', 'Tipo de transacción actualizado correctamente.', 'success');
					}
				},
				error: function(xhr) {
					Swal.fire('Error', 'Ocurrió un problema al procesar la solicitud.', 'error');
				}
			});
		});


		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function guardar_datos() {
			let form_data = $('#form').serializeArray();
			let url_guarda_datos = "{{ url('tipos_transacciones/ajax_store') }}";
			let type_guarda_datos = "POST";

			if ($('#accion').val() != "add") {
				let ttIdValue = form_data.find(item => item.name == 'id')?.value;
				url_guarda_datos = "{{ url('ttUpdate') }}" + "/" + ttIdValue;
				//url_guarda_datos = "{{ url('alertasUpdate') }}" + "/" + alertasIdValue;
				type_guarda_datos = "PUT";
				form_data.push({ name: '_method', value: 'PUT' });
			}
			show_loading();
			$.ajax({
				url: url_guarda_datos,
				type: type_guarda_datos,
				data: $.param(form_data),
				dataType: "JSON",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(data) {
					hide_loading();
					if (data.status === 0) {
						let errorMessage = data.message + "<br/>";
						if (data.errors) {
							for (let field in data.errors) {
								if (data.errors.hasOwnProperty(field)) {
									errorMessage += `${data.errors[field].join(", ")}<br/>`;
									//errorMessage += `${field.charAt(0).toUpperCase()}${field.slice(1).toLowerCase()}: ${data.errors[field].join(", ")}<br/>`;
								}
							}
						}
						swal.fire("Aviso", errorMessage, "warning");
					} else {
						swal.fire({
							title: "Aviso",
							text: data.message,
							icon: "success"
						}).then(() => {
							$('#modal_form').on('hidden.bs.modal', function () {
								limpiarErrores();
								$('#form')[0].reset(); // Opcional: resetear el formulario
							});
							$('#modal_form').modal('hide');
							reload_table();
						});
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					hide_loading();
					swal.fire("Error", "Ocurrió un problema en la solicitud. Intenta nuevamente.", "error");
					console.error(jqXHR.responseText);
				}
			});
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function validarFormulario() {
			let nombre = document.querySelector('input[name="nombre"]').value.trim();

			// Limpiar errores previos
			document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
			document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

			if (!nombre) errores.push({ campo: 'nombre', mensaje: "El campo 'Nombre' es obligatorio." });

			return true;
		}



		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function mostrarErrorGeneral(campo, mensaje) {
			let input = document.querySelector(`[name="${campo}"]`);
			if (input) {
				input.classList.add('is-invalid');
				let errorSpan = input.parentNode.querySelector('.invalid-feedback');
				if (!errorSpan) {
					errorSpan = document.createElement('span');
					errorSpan.classList.add('invalid-feedback');
					errorSpan.style.display = 'block';
					input.parentNode.appendChild(errorSpan);
				}
				errorSpan.innerHTML = mensaje;
			}
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function limpiarErrores() {
			// Remover clases de error
			document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

			// Eliminar mensajes de error
			document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

			// Limpiar el array de errores
			errores = [];
		}

	</script>

	<!--LISTADO-->
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h2>ABM Tipos de Transacciones</h2>
				@include('layouts.partials.message')
				@if ($errors->any())
					<div class="alert alert-danger">
						@foreach ($errors->all() as $error)
							{{ $error }}</br>
						@endforeach
					</div>
				@endif
				<div class="table-responsive">
					<div class="d-flex mb-2">
						<button id="agregar" class="btn btn-success mr-2" onclick="add_tipo_transaccion()">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
								<path d="M8 4v8m4-4H4" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
							</svg> {{ __('Agregar Tipo de Transacción') }}
						</button>
					</div>
					<table id="tipos_transacciones_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
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
								<label class="col-form-label col-md-3">Nombre</label>
								<div class="col-md-9">
									<input name="nombre" maxlength="255" placeholder="Nombre del tipo de transacción" class="form-control" type="text" required>
									<span class="help-block text-danger"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
					<a onclick="if (validarFormulario()) guardar_datos();" class="btn btn-primary">Guardar</a>
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</x-app-layout>