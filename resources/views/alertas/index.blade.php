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
			$("#form").submit(function(e) {
				e.preventDefault();
				var formData = new FormData(this);

				//show_loading();
				$.ajax({
					url: "{{ url('alertas/ajax_guardar_columna2') }}",
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
/* 			var td = '<tr><td><input class="form-control" type="text" maxlength="255" minlength="1" required name="valores[]"/></td><td><a class="btn btn-danger" onclick="eliminar_valor(this)"><i class="bi bi-trash"></i></td></tr>';
			$('#detalles_alertas tbody').append(td);
 */			const tableBody = document.querySelector("#detalles_alertas tbody");

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


<?php #dd($alertas); ?>

<div class="modal fade modal-lg" id="modal_form_alertas" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Editar alertas</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
				@csrf
                <div class="modal-body">
                    <!-- Campos principales -->
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" name="descripcion" required></textarea>
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
						<button type="submit" class="btn btn-primary" id="guardar_campo">Guardar</button>
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
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
			//console.log('id alerta ' + id);
			//console.log('url_guarda_datos ' + url_guarda_datos);

			$.ajax({
				url: url_guarda_datos,
				type: "GET",
				dataType: "JSON",
				success: function(response) {

					//console.log('Respuesta del servidor:', response.original.alertas.tipos_alertas_id); 
					//console.log('Respuesta del servidor:', response.alertas); 
					console.log('Respuesta del servidor:', response.alertas_detalles); 
					//console.log('Respuesta del servidor:', response.funciones); 

					// Asignar datos de la alerta principal al formulario
					//$('#form [name="id"]').val(response.alerta.id);
					$('#form [name="nombre"]').val(response.alertas.nombre);
					$('#form [name="descripcion"]').val(response.alertas.descripcion);
					$('#form [name="tipos_alertas_id"]').val(response.alertas.tipos_alertas_id);

					// Mostrar la modal
					$('#modal_form_alertas').modal('show');
					$('.modal-title').text('Editar Alerta');
					$('#form').attr('action', "{{ url('alertas.update') }}" + "/" + id);
					$('#method').val('PUT');

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