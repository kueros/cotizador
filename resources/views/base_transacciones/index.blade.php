<x-app-layout title="Base de Transacciones" :breadcrumbs="[['title' => 'Inicio', 'url' => '/dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Transacciones') }}
		</h2>
	</x-slot>
	<!-- Primary Navigation Menu -->
	@php
	$user = Auth::user()->username;
	$email = Auth::user()->email;
	$permiso_agregar_roles = tiene_permiso('add_transacciones');
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
			//tipo_transaccion_id = <?php #echo $id; ?>;
			table = $('#transacciones_table').DataTable({
				"ajax": {
					url: "{{ url('base_transacciones/listado') }}",
					type: 'GET',
/* 					data: function(d) { // Agrega parámetros adicionales a la solicitud
						d.tipo_transaccion_id = tipo_transaccion_id;
        			}
 */				},
				language: traduccion_datatable,
				//dom: 'Bfrtip',
				columnDefs: [{
					"targets": 'no-sort',
					"orderable": true
				}],
				layout: {
					topStart: {
						buttons: [{
								"extend": 'pdf',
								"text": 'Export',
								"className": 'btn btn-danger',
								"orientation": 'landscape',
								title: 'Transacciones'
							},
							{
								"extend": 'copy',
								"text": 'Export',
								"className": 'btn btn-primary',
								title: 'Transacciones'
							},
							{
								"extend": 'excel',
								"text": 'Export',
								"className": 'btn btn-success',
								title: 'Transacciones'
							},
							{
								"extend": 'print',
								"text": 'Export',
								"className": 'btn btn-secondary',
								title: 'Transacciones'
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
				},
				"order": [[2, 'asc']]
			});
		});

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function guardar_datos() {
			let form_data = $('#form').serializeArray();
			console.log('form_data ',form_data);
			let url_guarda_datos = "{{ url('transacciones/store') }}";
			let type_guarda_datos = "POST";

			if ($('#accion').val() != "add") {
				let transaccionesIdValue = form_data.find(item => item.name == 'id')?.value;
				if (!transaccionesIdValue) {
					console.error('El valor de transaccionesIdValue es inválido.');
					return;
				}
				url_guarda_datos = "{{ route('transacciones.update', ':id') }}".replace(':id', transaccionesIdValue);
				type_guarda_datos = "PUT";
				form_data.push({ name: '_method', value: 'PUT' });
			}
			show_loading();
			console.log("URL para la solicitud:", url_guarda_datos);
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
							$('#modal_transacciones').on('hidden.bs.modal', function () {
								limpiarErrores();
								$('#form')[0].reset(); // Opcional: resetear el formulario
							});
							$('#modal_transacciones').modal('hide');
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
			$('#modal_transacciones').on('hidden.bs.modal', function () {
				limpiarErrores();
				$('#form')[0].reset(); // Opcional: resetear el formulario
			});
					// Limpiar la tabla de detalles antes de llenarla
					//$('#detalles_alertas tbody').empty();

		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function validarFormulario() {
			console.log('validar formulario ');
			let nombre = document.querySelector('input[name="nombre"]').value.trim();
			let descripcion = document.querySelector('input[name="descripcion"]').value.trim();

			// Limpiar errores previos
			document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
			document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
			let errores = [];
			console.log('limpiar errores ');

			// Validación del campo 'nombre'
			if (!nombre) errores.push({ campo: 'nombre', mensaje: "El campo 'Nombre' es obligatorio." });
			if (nombre.length > 100) errores.push({ campo: 'nombre', mensaje: "El campo 'Nombre' no debe exceder los 100 caracteres." });
			if (nombre.length < 3) errores.push({ campo: 'nombre', mensaje: "El campo 'Nombre' no debe tener menos de 3 caracteres." });
			if (!/^[a-zA-Z0-9\s]+$/.test(nombre)) {
				errores.push({ campo: 'nombre', mensaje: "El campo 'Nombre' sólo puede contener letras, números y espacios." });
			}

			// Validación del campo 'descripcion'
			//if (!descripcion) errores.push({ campo: 'descripcion', mensaje: "El campo 'Descripción' es obligatorio." });
			if (descripcion.length > 100) errores.push({ campo: 'descripcion', mensaje: "El campo 'Descripcion' no debe exceder los 255 caracteres." });
			if (descripcion.length < 3) errores.push({ campo: 'descripcion', mensaje: "El campo 'Descripcion' no debe tener menos de 3 caracteres." });

			console.log('errores ', errores);
			if (errores.length > 0) {
				errores.forEach(err => mostrarErrorGeneral(err.campo, err.mensaje));
				return false;
			} else {
				return true;
			}
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function mostrarErroresPorFila(errores) {
			for (let filaIndex in errores) {
				let filaErrores = errores[filaIndex];
				let fila = document.querySelector(`#detalles_alertas tbody tr:nth-child(${parseInt(filaIndex) + 1})`);
				if (fila) {
					for (let campo in filaErrores) {
						let mensajes = filaErrores[campo];
						let input = fila.querySelector(`[name="${campo}[]"]`);
						if (input) {
							input.classList.add('is-invalid');
							let errorSpan = input.parentNode.querySelector('.invalid-feedback');
							if (!errorSpan) {
								errorSpan = document.createElement('span');
								errorSpan.classList.add('invalid-feedback');
								errorSpan.style.display = 'block';
								input.parentNode.appendChild(errorSpan);
							}
							errorSpan.innerHTML = mensajes.join('<br>');
						}
					}
				}
			}
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function mostrarErrorGeneral(campo, mensaje) {
			let input = document.querySelector(`[name="${campo}"]`) || document.querySelector(`#${campo}`);
			if (input) {
				if (campo === 'detalles_alertas') {
					let tabla = document.querySelector(`#${campo}`);
					if (tabla) {
						let errorSpan = tabla.parentNode.querySelector('.invalid-feedback');
						if (!errorSpan) {
							errorSpan = document.createElement('span');
							errorSpan.classList.add('invalid-feedback');
							errorSpan.style.display = 'block';
							errorSpan.style.color = 'red';
							tabla.parentNode.appendChild(errorSpan);
						}
						errorSpan.innerHTML = mensaje;
					}
				} else {
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

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function reload_table() {
			table.ajax.reload(null, false);
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function add_transacciones() {
			save_method = 'add';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#modal_transacciones').modal('show');
			$('.modal-title').text('Agregar Transacción');
			$('#accion').val('add');
			$('#form').attr('action', "{{ url('transacciones') }}");
			$('#method').val('POST');
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function edit_transacciones(id) {
			save_method = 'update';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');

			$.ajax({
				url: "{{ route('transacciones.edit', ':id') }}".replace(':id', id),
				type: "GET",
				dataType: "JSON",
				success: function(data) {
					// Asigna los valores al formulario
					$('#form [name="id"]').val(data.id);
					$('#form [name="nombre"]').val(data.nombre);
					$('#form [name="descripcion"]').val(data.descripcion);
					$('#modal_transacciones').modal('show');
					$('.modal-title').text('Editar Transacción');
					$('#form').attr('action', "{{ url('transacciones') }}" + "/" + id);
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
		function delete_transacciones(id) {
			if (confirm('¿Desea borrar esta transacción?')) {

				$.ajax({
					url: "{{ route('transacciones.delete', ':id') }}".replace(':id', id),
					type: "POST",
					dataType: "JSON",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						swal.fire("Aviso", "Transacción eliminada con éxito.", "success");

						$('#modal_transacciones').modal('hide');
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
				<h2>Base de Transacciones</h2>
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
<!-- 					<div class="d-flex mb-2">
						<button id="agregar" class="btn btn-success mr-2" onclick="add_transacciones()">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
								<path d="M8 4v8m4-4H4" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
							</svg> {{ __('Agregar Transacción') }}
						</button>
					</div>
 -->
					<table id="transacciones_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
						<th>Tipos de Transacciones</th>
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
		#dd($tipos_transacciones);
		#dd($ultima_posicion);
	?>
	<?php
	?>

	<div class="modal fade" id="modal_transacciones" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Base de Transacciones</h5>
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
								<label class="col-form-label col-md-3">{{ __('Tipos de Transacciones') }}</label>
								<div class="col-md-9">
									<input name="nombre" minlength="3" maxlength="100" placeholder="Nombre de la Transacción"
										id="nombre" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<a onclick=" if (validarFormulario())  guardar_datos();" class="btn btn-primary">Guardar</a>
						<a id="eliminar_filas" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</a>
					</div>
				</form>
			</div>
		</div>
	</div>

</x-app-layout>