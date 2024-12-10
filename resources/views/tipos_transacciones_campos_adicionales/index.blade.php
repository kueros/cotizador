<x-app-layout title="Campos adicionales" :breadcrumbs="[['title' => 'Inicio', 'url' => '/dashboard'],['title' => 'ABM Tipos de Transacciones', 'url' => '/tipos_transacciones']]">
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
	<?php
	#dd($id); 
	?>
	<script type="text/javascript">
		var table;
		var save_method;
		jQuery(document).ready(function($) {
			tipo_transaccion_id = <?php echo $id; ?>;
			table = $('#tipos_transacciones_table').DataTable({
				"ajax": {
					url: "{{ url('tipos_transacciones_campos_adicionales/ajax_listado') }}",
					type: 'GET',
					data: function(d) {
						d.tipo_transaccion_id = tipo_transaccion_id;
					}
				},
				language: traduccion_datatable,
				dom: 'Bfrtip',
				buttons: [{
						extend: 'pdf',
						text: 'Exportar PDF',
						className: 'btn btn-danger',
						orientation: 'landscape',
						title: 'Campos Adicionales <?php echo $tipo_transaccion_nombre; ?>',
					},
					{
						extend: 'copy',
						text: 'Copiar',
						className: 'btn btn-primary',
						exportOptions: {
							columns: [0]
						}
					},
					{
						extend: 'excel',
						text: 'Exportar Excel',
						className: 'btn btn-success',
						exportOptions: {
							columns: [0]
						}
					},
					{
						extend: 'print',
						text: 'Imprimir',
						className: 'btn btn-secondary',
						exportOptions: {
							columns: [0]
						}
					}
				],
				columnDefs: [{
					targets: 'no-sort',
					orderable: true
				}],
				initComplete: function() {
					$('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
					$('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
					$('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
					$('.buttons-print').html('<span class="bi bi-printer"></span> Imprimir');
				},
				order: [
					[2, 'asc']
				]
			});
		});

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function reload_table() {
			table.ajax.reload(null, false);
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function add_campo_adicional() {
			save_method = 'add';
			$('#form_campo_adicional')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			//$('#modal_form_campo_adicional').modal('show');
			$('#modal_form_campo_adicional').modal('show');
			$('.modal-title').text('Agregar campo adicional');
			$('#accion').val('add');
			$('#form_campo_adicional').attr('action', "{{ url('tipos_transacciones_campos_adicionales') }}");
			$('#method').val('POST');
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function edit_campos_adicionales(id) {
			save_method = 'update';
			$('#form_campo_adicional')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');

			$.ajax({
				url: "{{ route('tipos_transacciones_campos_adicionales.ajax_edit', ':id') }}".replace(':id', id),
				type: "GET",
				dataType: "JSON",
				success: function(data) {
					console.log(data);
					$('[name="id"]').val(data.id);
					$('[name="nombre_campo"]').val(data.nombre_campo);
					$('[name="nombre_mostrar"]').val(data.nombre_mostrar);
					$('[name="visible"]').val(data.visible);
					$('[name="orden_listado"]').val(data.orden_listado);
					$('[name="requerido"]').val(data.requerido);
					$('[name="tipo"]').val(data.tipo);
					$('[name="valores"]').val(data.valores);
					$('[name="posicion"]').val(data.orden_listado);
					$('[name="valor_default"]').val(data.valor_default);

					$('[name="visible"]').prop('checked', data.visible == 1);
					$('[name="requerido"]').prop('checked', data.requerido == 1);

					if (data.tipo == 4) {
						$('#div_valores_selector').show();

						// Limpiar contenido previo de la tabla
						$('#valores_selector tbody').empty();
						if (typeof data.valores === 'string') {
							data.valores = JSON.parse(data.valores);
						}
						if (data.valores && Array.isArray(data.valores)) {
							data.valores.forEach(valor => {
								var fila = `
									<tr>
										<td><input class="form-control" type="text" value="${valor}" maxlength="255" minlength="1" required name="valores[]"/></td>
										<td><a class="btn btn-danger" onclick="eliminar_valor(this)"><i class="bi bi-trash"></i></a></td>
									</tr>`;
								$('#valores_selector tbody').append(fila);
								console.log('fila ', fila);
							});
						}
					} else {
						$('#div_valores_selector').hide();
					}

					$('#modal_form_campo_adicional').modal('show');
					$('.modal-title').text('Editar campo adicional de tipo de transacción');
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

						$('#modal_form_campo_adicional').modal('hide');
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
		function validar_campos_requeridos(form_id) {
			var form_status = true;

			$($('#' + form_id).find(':input')).each(function() {
				if ($(this).prop("required") && !$(this).prop("disabled")) {
					if ($(this).val()) {
						if (!$(this)[0].checkValidity()) {
							$(this).addClass('field-required');
							form_status = false;
						} else {
							$(this).removeClass('field-required');
						}
					} else {
						$(this).addClass('field-required');
						form_status = false;
					}
				}
			});

			return form_status;
		}
		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function guardar_datos() {
			let form_data = $('#form_campo_adicional').serializeArray();
			let url_guarda_datos = "{{ url('tipos_transacciones_campos_adicionales/ajax_store') }}";
			let type_guarda_datos = "POST";

			if (!validar_campos_requeridos('form_campo_adicional')) {
				$('#form_campo_adicional')[0].reportValidity();
				return false;
			}

			if ($('#accion').val() != "add") {
				let id = $('[name="id"]').val();
				if (!id) {
					console.error('El campo ID está vacío.');
					swal.fire("Error", "El ID no está definido.", "error");
					return false;
				}
				url_guarda_datos = "{{ url('tipos_transacciones_campos_adicionales') }}" + "/" + id;
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
		function cambiar_tipo_campo(selector) {
			//alert(selector);
			var tipo_campo = $(selector).val();
			switch (tipo_campo) {
				case '4':
					$('#div_valores_selector').show();
					break;
				default:
					$('#div_valores_selector').hide();
					break;
			}
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function eliminar_valor(button) {
			// Obtener la fila <tr> que contiene el botón de eliminar
			var row = button.closest('tr');

			// Eliminar la fila
			row.remove();
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function agregar_valor_selector() {
			var td = '<tr><td><input class="form-control" type="text" maxlength="255" minlength="1" required name="valores[]"/></td><td><a class="btn btn-danger" onclick="eliminar_valor(this)"><i class="bi bi-trash"></i></td></tr>';
			$('#valores_selector tbody').append(td);
		}
	</script>
	<!--LISTADO-->
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h2>Campos Adicionales <?php echo $tipo_transaccion_nombre; ?></h2>
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
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
								<path d="M8 4v8m4-4H4" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
							</svg> {{ __('Agregar Campo Adicional') }}
						</button>
					</div>

					<table id="tipos_transacciones_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<th>Nombre</th>
							<th>Alias (Nombre de columna)</th>
							<th>Posición</th>
							<th>Requerido</th>
							<th>Tipo de campo</th>
							<th>Valores</th>
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


	<div class="modal fade" id="modal_form_campo_adicional" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Formulario campos adicionales</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="form_campo_adicional" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
					@csrf
					<input name="_method" type="hidden" id="method">
					<div class="modal-body form">
						<input name="tipo_transaccion_id" id="tipo_transaccion_id" class="form-control" type="hidden" value="<?php echo $id; ?>">
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

						<div class="form-body">
							<div class="row mb-3">
								<label class="col-md-3 col-form-label">{{ __('Visibilidad en Formulario?') }}</label>
								<div class="col-md-9">
									<select name="visible" class="form-control" required>
										<option value="">Seleccionar</option>
										<option value="1">Sí</option>
										<option value="0">No</option>
									</select>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Orden en el Formulario') }}</label>
								<div class="col-md-9">
									<input name="posicion" placeholder="1"
										id="posicion" class="form-control" type="number" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="row mb-3">
								<label class="col-md-3 col-form-label">{{ __('Es Obligatorio?') }}</label>
								<div class="col-md-9">
									<select name="requerido" class="form-control" required>
										<option value="">Seleccionar</option>
										<option value="1">Sí</option>
										<option value="0">No</option>
									</select>
								</div>
							</div>
						</div>

						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Tipo de Campo') }}</label>
								<div class="col-md-9">
									<select id="tipo" name="tipo" class="mt-1 block w-full form-control" onchange="cambiar_tipo_campo(this)">
										<option value="">
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
						<div id="div_valores_selector" class="form-group" style="display:none">
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
						<a onclick="guardar_datos()" class="btn btn-primary">{{ __('Guardar') }}</a>
						<!--a type="submit" class="btn btn-primary">{{ __('Guardar') }}</a-->
						<a class="btn btn-danger" data-bs-dismiss="modal">Cancelar</a>
					</div>
				</form>
			</div>
		</div>
	</div>
    <script>
        $.get("{{ url('tipos_transacciones_campos_adicionales/ajax_listado') }}", function(data) {
            console.log(data); // Asegúrate de que el JSON sea válido y contenga la clave 'data'
        });
    </script>
</x-app-layout>