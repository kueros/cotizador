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
			console.log('accion1: ', $('#accion').val());
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
			//console.log('Contenido de form_data (JSON):', JSON.stringify(form_data, null, 2));
			console.log('accion: ', $('#accion').val());
			let url_guarda_datos = "{{ url('alertas/ajax_guardar_columna') }}";
			//let url_guarda_datos = "{{ url('alertasIndex') }}";
			let type_guarda_datos = "POST";

			if ($('#accion').val() != "add") {
				console.log('form_data: ', form_data);
				let alertasIdValue = form_data.find(item => item.name == 'alertas_id')?.value;
				console.log('alertasIdValue: ', alertasIdValue);
				url_guarda_datos = "{{ url('alertasUpdate') }}" + "/" + alertasIdValue;
				type_guarda_datos = "PUT";
				form_data.push({ name: '_method', value: 'PUT' });
				console.log('url_guarda_datos: ', url_guarda_datos);
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
					if (data.status == 0) {
						let errorMessage = data.message + "</br>";
						mostrarErroresPorFila(data.errors);
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
		function validarFormulario() 
		{
			let errores = [];
			let nombre = document.querySelector('input[name="nombre"]').value.trim();
			let descripcion = document.querySelector('input[name="descripcion"]').value.trim();
			let tiposAlertasId = document.querySelector('select[name="tipos_alertas_id"]').value;
			let funcionesId = document.querySelectorAll('select[name="funciones_id[]"]');
			let fechasDesde = document.querySelectorAll('input[name="fecha_desde[]"]');
			let fechasHasta = document.querySelectorAll('input[name="fecha_hasta[]"]');

			if (!nombre) errores.push("El campo 'Nombre' es obligatorio.");
			if (!descripcion) errores.push("El campo 'Descripción' es obligatorio.");
			if (!tiposAlertasId || tiposAlertasId === "0") errores.push("Seleccione un 'Tipo de Alerta'.");

			funcionesId.forEach((funcion, index) => {
				if (!funcion.value || funcion.value === "0") {
					errores.push(`La fila ${index + 1} necesita un valor válido en 'Función ID'.`);
				}
			});

			fechasDesde.forEach((fecha, index) => {
				if (!fecha.value) errores.push(`La fila ${index + 1} necesita una 'Fecha Desde'.`);
			});

			fechasHasta.forEach((fecha, index) => {
				if (!fecha.value) errores.push(`La fila ${index + 1} necesita una 'Fecha Hasta'.`);
				else if (new Date(fechasDesde[index].value) > new Date(fecha.value)) {
					errores.push(`En la fila ${index + 1}, 'Fecha Hasta' debe ser mayor o igual a 'Fecha Desde'.`);
				}
			});

			if (errores.length > 0) {
				swal.fire("Aviso", "Errores:\n" + errores.join("\n"), "warning");
				
				//alert("Errores:\n" + errores.join("\n"));
				return false;
			}
			return true;
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function mostrarErroresPorFila(errores) {
			// Iterar sobre las filas que tienen errores
			for (let filaIndex in errores) {
				let erroresFila = errores[filaIndex];

				// Seleccionar la fila correspondiente en la tabla
				let fila = document.querySelector(`#detalles_alertas tbody tr:nth-child(${parseInt(filaIndex) + 1})`);

				if (fila) {
					// Iterar sobre los campos con errores en esa fila
					for (let campo in erroresFila) {
						let mensajes = erroresFila[campo];

						// Buscar el input o select correspondiente dentro de la fila
						let input = fila.querySelector(`[name="${campo}[]"]`);
						if (input) {
							// Mostrar el error como un tooltip o al lado del campo
							input.classList.add('is-invalid');

							// Crear un span para mostrar el mensaje de error (si no existe)
							let errorSpan = input.parentNode.querySelector('.invalid-feedback');
							if (!errorSpan) {
								errorSpan = document.createElement('span');
								errorSpan.classList.add('invalid-feedback');
								errorSpan.style.display = 'block'; // Asegurar que sea visible
								input.parentNode.appendChild(errorSpan);
							}
							errorSpan.innerHTML = mensajes.join(', ');
						}
					}
				}
			}
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function agregar_valor_selector() {
			const tableBody = document.querySelector("#detalles_alertas tbody");

			// Crear una nueva fila con los campos necesarios
			const newRow = document.createElement("tr");

			newRow.innerHTML = `
				<td>
					<select class="form-control" name="funciones_id[]" required>
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
						<i class="fa fa-trash"></i> Eliminar
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
#dd($funciones);
?>
				<div class="table-responsive">
					<div class="d-flex mb-2">
						<button id="agregar" class="btn btn-success mr-2" onclick="add_alerta()">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
								<path d="M8 4v8m4-4H4" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
							</svg> {{ __('Agregar Alerta') }}
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


<?php #dd($alertas->first()['id']); ?>

<div class="modal fade modal-lg" id="modal_form_alertas" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Editar alertas</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
				@csrf
				<input name="_method" type="hidden" id="method">
				<input name="alertas_id" id="alertas_id" class="form-control" type="hidden" value="<?php echo $alertas->first()['id'] ?? ""; ?>">
				<input type="hidden" value="" name="accion" id="accion" />
				<input type="hidden" value="" name="id" />
                <div class="modal-body">
                    <!-- Campos principales -->
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <input type="text" class="form-control" name="descripcion" required>
                    </div>
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
                    <h5>Detalles de la Alerta</h5>
					<a class="btn btn-success" onclick="agregar_valor_selector()">Agregar valor</a>

                    <table class="table table-bordered" id="detalles_alertas">
                        <thead>
                            <tr>
                                <th>Función ID</th>
                                <th>Fecha Desde</th>
                                <th>Fecha Hasta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Filas dinámicas se añadirán aquí -->
                        </tbody>
                    </table>
                </div>
				<div class="modal-footer">
					<a onclick="if (validarFormulario()) guardar_datos();" class="btn btn-primary">Guardar</a>
					<a class="btn btn-danger" data-bs-dismiss="modal">Cancelar</a>
				</div>
            </form>
        </div>
    </div>
</div>
<script>

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
					//$('#form [name="id"]').val(response.alerta.id);
					$('#form [name="nombre"]').val(response.alertas.nombre);
					$('#form [name="descripcion"]').val(response.alertas.descripcion);
					$('#form [name="tipos_alertas_id"]').val(response.alertas.tipos_alertas_id);

					// Mostrar la modal
					$('#modal_form_alertas').modal('show');
					$('.modal-title').text('Editar Alerta');
					//$('#form').attr('action', "{{ url('alertas') }}" + "/" + id);
					//$('#method').val('PUT');

					// Limpiar la tabla de detalles antes de llenarla
					$('#detalles_alertas tbody').empty();
					//console.log('funciones ' + response.funciones.length);

					// Asignar los datos de detalles_alertas
					if (response.alertas_detalles.length > 0) {
						response.alertas_detalles.forEach(function(detalle) {
							let opcionesFunciones = '';
							response.funciones.forEach(function(funcion) {
								const selected = funcion.id == detalle.funciones_id ? 'selected' : '';
								opcionesFunciones += `<option value="${funcion.id}" ${selected}>${funcion.nombre}</option>`;
								//console.log('funcion.id '+`<option value="${funcion.id}" ${funcion.id == detalle.funciones_id ? "selected" : ""}>${funcion.nombre}</option>`);
							});
							//console.log('selected '+opcionesFunciones);
							const row = `
								<tr>
										<input type="hidden" name="detalles_id[]" value="${detalle.id}">
										<input type="hidden" name="alertas_id[]" value="${detalle.alertas_id}">
									<td>
										<select class="form-control" name="funciones_id[]" required>
											${opcionesFunciones}
										</select>
									</td>
									<td>
										<input type="date" class="form-control" name="fecha_desde[]" value="${detalle.fecha_desde}">
									</td>
									<td>
										<input type="date" class="form-control" name="fecha_hasta[]" value="${detalle.fecha_hasta}">
									</td>
									<td>
										<button type="button" class="btn btn-danger btn-sm" onclick="remove_row(this)">
											<i class="fa fa-trash"></i>
										</button>
									</td>
								</tr>`;
							$('#detalles_alertas tbody').append(row);
						});
					}
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

		// Función para eliminar una fila de detalles
		function remove_row(button) {
			$(button).closest('tr').remove();
		}
	</script>

</x-app-layout>