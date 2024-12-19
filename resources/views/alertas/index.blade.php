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
	#dd($response);
	?>
	<script type="text/javascript">
		var table;
		var save_method;
		jQuery(document).ready(function($) {
			table = $('#alertas_table').DataTable({
				"ajax": {
					url: "{{ url('alertas/ajax_listado') }}",
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
		function add_alerta() {
			save_method = 'add';
			eliminarValores();
			$('#detalles_alertas tbody').empty();
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			limpiarErrores();
			$('#detalles_alertas tbody').empty();
			$('#modal_form_alertas').modal('show');
			$('.modal-title').text('Agregar Alerta');
			$('#accion').val('add');
			$('#form').attr('action', "{{ url('alertas') }}");
			$('#method').val('POST');
			console.log('accion1: ', $('#accion').val());
			console.log('errores ',errores);
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function delete_alerta(id) {
			if (confirm('¿Desea borrar esta alerta?')) {

				$.ajax({
					url: "{{ route('alertas.ajax_delete', ':id') }}".replace(':id', id),
					type: "POST",
					dataType: "JSON",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						swal.fire("Aviso", "Alerta eliminada con éxito.", "success");

						$('#modal_form_alertas').on('hidden.bs.modal', function () {
							limpiarErrores();
							$('#form')[0].reset(); // Opcional: resetear el formulario
						});
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
			console.log('form_data ',form_data);
			let url_guarda_datos = "{{ url('alertas/ajax_store') }}";
			let type_guarda_datos = "POST";

			if ($('#accion').val() != "add") {
				let alertasIdValue = form_data.find(item => item.name == 'alertas_id')?.value;
				if (!alertasIdValue) {
					console.error('El valor de alertasIdValue es inválido.');
					return;
				}
				// url_guarda_datos = "{{ url('alertasUpdate') }}" + "/" + alertasIdValue;
				url_guarda_datos = "{{ route('alertasUpdate', ':id') }}".replace(':id', alertasIdValue);
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
							$('#modal_form_alertas').on('hidden.bs.modal', function () {
								limpiarErrores();
								$('#form')[0].reset(); // Opcional: resetear el formulario
							});
							$('#modal_form_alertas').modal('hide');
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
			$('#modal_form_alertas').on('hidden.bs.modal', function () {
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
			let tiposAlertasId = document.querySelector('select[name="tipos_alertas_id"]').value;
			//let tiposTratamientosId = document.querySelector('select[name="tipos_tratamientos_id"]').value;
			let funcionesId = document.querySelectorAll('select[name="funciones_id[]"]');
			let fechasDesde = document.querySelectorAll('input[name="fecha_desde[]"]');
			let fechasHasta = document.querySelectorAll('input[name="fecha_hasta[]"]');
			let hoy = new Date();
			hoy.setHours(0, 0, 0, 0);
			let detallesTable = document.querySelector('#detalles_alertas tbody');

			// Limpiar errores previos
			document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
			document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
			errores = [];
			console.log('limpiar errores ');

			if (!nombre) errores.push({ campo: 'nombre', mensaje: "El campo 'Nombre' es obligatorio." });
			if (nombre.length > 100) errores.push({ campo: 'nombre', mensaje: "El campo 'Nombre' no debe exceder los 100 caracteres." });
			if (nombre.length < 3) errores.push({ campo: 'nombre', mensaje: "El campo 'Nombre' no debe tener menos de 3 caracteres." });
			if (!descripcion) errores.push({ campo: 'descripcion', mensaje: "El campo 'Descripción' es obligatorio." });
			if (!tiposAlertasId || tiposAlertasId === "0") errores.push({ campo: 'tipos_alertas_id', mensaje: "El campo 'Tipo de Alerta' es obligatorio." });
			//if (!tiposTratamientosId || tiposTratamientosId === "0") errores.push({ campo: 'tipos_tratamientos_id', mensaje: "El campo 'Tipo de Tratamiento' es obligatorio." });

			// Validar contenido de la tabla 'detalles_alertas'
			if (!detallesTable || detallesTable.children.length === 0) {
				errores.push({ campo: 'detalles_alertas', mensaje: "Debe agregar al menos un dato en la tabla de detalles." });
			}

			// Validar filas de la tabla
			let filaErrores = [];
			funcionesId.forEach((funcion, index) => {
				let erroresFila = {};
				if (!funcion.value || funcion.value === "0") {
					erroresFila['funciones_id'] = ["Debe seleccionar una función válida."];
				}
				if (fechasDesde[index] && !fechasDesde[index].value) {
					erroresFila['fecha_desde'] = ["La 'Fecha Desde' es obligatoria."];
				}
				if (fechasHasta[index] && !fechasHasta[index].value) {
					erroresFila['fecha_hasta'] = ["La 'Fecha Hasta' es obligatoria."];
				} else if (new Date(fechasDesde[index].value) > new Date(fechasHasta[index].value)) {
					erroresFila['fecha_hasta'] = ["La 'Fecha Hasta' debe ser mayor o igual a 'Fecha Desde'."];
				}

				if (Object.keys(erroresFila).length > 0) {
					filaErrores[index] = erroresFila;
				}
			});
			console.log('filaErrores.length ',filaErrores.length);
			console.log('errores ',errores);
			if (filaErrores.length > 0 || errores.length > 0) {
				mostrarErroresPorFila(filaErrores);
				errores.forEach(err => mostrarErrorGeneral(err.campo, err.mensaje));
				return false;
			} else {
				// Limpiar la tabla de detalles antes de llenarla
				//$('#detalles_alertas tbody').empty();
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
		function agregar_valor_selector() {
			const tableBody = document.querySelector("#detalles_alertas tbody");

			// Crear una nueva fila con los campos necesarios
			const newRow = document.createElement("tr");

			newRow.innerHTML = `
				<td>
					<select class="form-control" name="funciones_id[]" >
						<option value="0">Elija una Función</option>
						@foreach($funciones as $funcion)
						<option value="{{ $funcion->id }}">{{ $funcion->nombre }}</option>
						@endforeach
					</select>
				</td>
				<td>
					<input type="date" name="fecha_desde[]" class="form-control" required>
				</td>
				<td>
					<input type="date" name="fecha_hasta[]" class="form-control" required>
				</td>
				<td>
					<button type="button" class="btn btn-danger btn-sm" onclick="remove_row(this)">
						<i class="fa fa-trash"></i> 
					</button>
				</td>
			`;

			// Agregar la fila a la tabla
			tableBody.appendChild(newRow);
		}

		// Función para eliminar una fila
		function remove_row(button) {
			const row = button.closest("tr");
			row.remove();
		}
 		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
 		// Función para eliminar los valores dinámicos
		function eliminarValores() {
			console.log('errores ',errores);
			const tablaDinamica = document.getElementById("detalles_alertas");
			
			// Elimina todas las filas dinámicas
			while (tablaDinamica.firstChild) {
				tablaDinamica.removeChild(tablaDinamica.firstChild);
			}
		}

		// Evento para asociar el botón "Cancelar" a la función "eliminarValores".
		document.getElementById("btn-cancelar").addEventListener("click", function() {
			eliminarValores(); // Llama a la función para limpiar la tabla
		});

		
/*
 		/*******************************************************************************************************************************
		 *******************************************************************************************************************************
		function eliminar_valor(button) {
			// Obtener la fila <tr> que contiene el botón de eliminar
			var row = button.closest('tr');
			
			// Eliminar la fila
			row.remove();
		}
 */	
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
				<div class="table-responsive">
					<div class="d-flex mb-2">
						<button id="agregar" class="btn btn-success mr-2" onclick="add_alerta()">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
								<path d="M8 4v8m4-4H4" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
							</svg> {{ __('Agregar Alerta') }}
						</button>
						<a class="btn btn-primary" href="{{ route('alertas_tipos') }}" title="Detalle Alerta">{{ __('Administrar Tipos de Alerta') }}</a>
						<a class="btn btn-primary" href="{{ route('alertas_tipos_tratamientos') }}" title="Tipos de Tratamientos">{{ __('Administrar Tipos de Tratamientos') }}</a>
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

	<div class="modal fade modal-lg" id="modal_form_alertas" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Editar Alertas</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" style="padding-top:0px;">
					<form id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
						@csrf
						<input name="_method" type="hidden" id="method">
						<input name="alertas_id" id="alertas_id" class="form-control" type="hidden" value="<?php echo $alertas->first()['id'] ?? ""; ?>">
						<input type="hidden" value="" name="accion" id="accion" />
						<input type="hidden" value="" name="id" />
						<!-- Campos principales -->
						<div class="form-group" style="margin-top:15px; margin-bottom:15px;">
							<label for="nombre">Nombre</label>
							<input type="text" class="form-control" name="nombre" required>
						</div>
						<div class="form-group" style="margin-top:15px; margin-bottom:15px;">
							<label for="descripcion">Descripción</label>
							<input type="text" class="form-control" name="descripcion" required>
						</div>
<?php #dd($tipos_alertas); ?>
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
						<!-- Detalles de Alerta -->
						<div  style="margin-top:15px; margin-bottom:15px;">
							<h5>Detalles de la Alerta</h5>
						</div>
						<div class="form-group">
							<a class="btn btn-success" onclick="agregar_valor_selector()">Agregar valor</a>
							<table class="table table-bordered" id="detalles_alertas" style="margin-top:15px; margin-bottom:15px;">
								<thead>
									<tr>
										<th>Función</th>
										<th>Fecha Desde</th>
										<th>Fecha Hasta</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<!-- Filas dinámicas se añadirán aquí -->
								</tbody>
							</table>
							<span class="invalid-feedback" style="display: none; color: red;"></span>
						</div>
						<div class="modal-footer">
							<a onclick="if (validarFormulario()) guardar_datos();" class="btn btn-primary">Guardar</a>
							<a id="eliminar_filas" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script>
		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function formatDateToISO(date) {
			console.log('date ',date);
			const [day, month, year] = date.split("-");
			//return `${day}-${month}-${year}`;
			return `${year}-${month}-${day}`;
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function edit_alertas(id) {
			save_method = 'update';
			$('#form')[0].reset(); // Resetea el formulario principal
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');
			let url_guarda_datos = "{{ route('alertas.ajax_edit', ':id') }}".replace(':id', id);
			console.log('id alerta ' + id);
			//console.log('url_guarda_datos ' + url_guarda_datos);

			$.ajax({
				url: url_guarda_datos,
				type: "GET",
				dataType: "JSON",
				success: function(response) {
					$('#form [name="nombre"]').val(response.alertas.nombre);
					$('#form [name="descripcion"]').val(response.alertas.descripcion);
					$('#form [name="tipos_alertas_id"]').val(response.alertas.tipos_alertas_id);
					//$('#form [name="tipos_tratamientos_id"]').val(response.alertas.tipos_tratamientos_id);
					// Mostrar la modal
					$('#modal_form_alertas').modal('show');
					$('.modal-title').text('Editar Alerta');
					// Limpiar la tabla de detalles antes de llenarla
					$('#detalles_alertas tbody').empty();
					// Asignar los datos de detalles_alertas
					if (response.alertas_detalles.length > 0) {
						response.alertas_detalles.forEach(function(detalle) {
							console.log('detalle ',detalle);
							const funcionesIds = detalle.funciones_id.split(',');
							const fechasDesde = detalle.fecha_desde.split(',');
							const fechasHasta = detalle.fecha_hasta.split(',');

							for (let i = 0; i < funcionesIds.length; i++) {
								// Convertir las fechas a formato yyyy-MM-dd

console.log('fechasDesde[i] ',fechasDesde[i]);
console.log('fechasHasta[i] ',fechasHasta[i]);
								fechaDesdeFormatted = formatDateToISO(fechasDesde[i]);
								fechaHastaFormatted = formatDateToISO(fechasHasta[i]);
console.log('fechaDesdeFormatted ',fechaDesdeFormatted);
console.log('fechaHastaFormatted ',fechaHastaFormatted);
								let opcionesFunciones = '';
								response.funciones.forEach(function(funcion) {
									const selected = funcion.id == funcionesIds[i] ? 'selected' : '';
									opcionesFunciones += `<option value="${funcion.id}" ${selected}>${funcion.nombre}</option>`;
								});

								const row = `
									<tr>
										<input type="hidden" name="detalles_id[]" value="${detalle.id}">
										<input type="hidden" name="alertas_id[]" value="${detalle.alertas_id}">
										<td>
											<select class="form-control" name="funciones_id[]" >
												${opcionesFunciones}
											</select>
										</td>
										<td>
											<input type="date" class="form-control" name="fecha_desde[]" value="${fechaDesdeFormatted}">
										</td>
										<td>
											<input type="date" class="form-control" name="fecha_hasta[]" value="${fechaHastaFormatted}">
										</td>
										<td>
											<button type="button" class="btn btn-danger btn-sm" onclick="remove_row(this)">
												<i class="fa fa-trash"></i>
											</button>
										</td>
									</tr>`;

								$('#detalles_alertas tbody').append(row);
							}
						});
					}
				}
			});
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		// Función para eliminar una fila de detalles
		function remove_row(button) {
			console.log("Eliminando fila...");
			$(button).closest('tr').remove();
		}
		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function eliminarValores() 
		{
			// Selecciona el botón de eliminar
			const botonEliminar = document.querySelector("#eliminar_filas");
			if (botonEliminar) {
				botonEliminar.addEventListener("click", function () {
					// Selecciona el tbody dentro de la tabla
					$(document).on('click', '#eliminar_filas', function () {
						const tablaBody = document.querySelector("#detalles_alertas tbody");
						if (tablaBody) {
							console.log("Eliminando todas las filas dinámicas...");
							while (tablaBody.firstChild) {
								tablaBody.removeChild(tablaBody.firstChild);
							}
						} else {
							console.error("No se encontró un <tbody> en la tabla.");
						}
					});				
				});
			} else {
				console.error("El botón con ID #eliminar_filas no existe en el DOM.");
			}
		}
		// Llamar a la función eliminarValores
		document.addEventListener("DOMContentLoaded", eliminarValores);
		$('#modal_form_alertas').on('hidden.bs.modal', function () {
			limpiarErrores();
			$('#form')[0].reset(); // Opcional: resetear el formulario
			$('#detalles_alertas tbody').empty(); // Vaciar las filas dinámicas
		});
	</script>
</x-app-layout>