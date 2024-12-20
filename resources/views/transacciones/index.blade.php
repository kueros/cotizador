<?php
	use App\Models\TipoTransaccion;
?>

<x-app-layout title="Transacciones" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard'],['title' => 'Base de Transacciones', 'url' => '/base_transacciones']]">
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

	<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
	<script type="text/javascript">
		var table;
		var save_method;
		//import * as XLSX from 'xlsx';

		jQuery(document).ready(function($) {
			tipos_transacciones_id = <?php echo $id; ?>;
			
			// Solicitar la estructura dinámica de la tabla
			$.ajax(
			{
				url: "{{ url('transacciones/listado') }}" + '/' + tipos_transacciones_id,
				type: 'GET',
				headers: {
					'Accept': 'application/json'
				},
				success: function(response) {
					console.log('respuesta: ', response);
					if (!response.columns || !response.data) {
						console.error("La respuesta no tiene los datos esperados:", response);
						return;
					}

					// Crear las columnas dinámicamente usando el nombre del campo
					var columns = response.columns.map(function(col) {
						return {
							title: col.nombre_mostrar,
							data: col.nombre_campo // Usar el nombre del campo como clave
						};
					});

					// Agregar una columna para las acciones al final 
					columns.push({
						title: 'Acciones',
						data: null,  // Las acciones no se mapearán desde los datos, sino que se agregarán manualmente
						orderable: false,
						searchable: false,
						className: 'no-export', // Clase para identificar esta columna
						render: function(data, type, row) {
							console.log('Fila:', row);
							return '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_transacciones(' + row.id + ')"><i class="bi bi-pencil-fill"></i></a>' +
								'<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_transacciones(' + row.id + ')"><i class="bi bi-trash"></i></a>';
						}
					});
					// Inicializar DataTable con columnas dinámicas
					table = $('#transacciones_table').DataTable({
						ajax: {
							url: "{{ url('transacciones/listado') }}" + '/' + tipos_transacciones_id,
							type: "GET",
							headers: {
								'Accept': 'application/json'
							},
							dataSrc: 'data' // Define la clave donde están los datos en la respuesta JSON
						},
						columns: columns,
						language: traduccion_datatable,
						dom: 'Bfrtip',
						buttons: [
							{
								extend: 'excel',
								text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
								className: 'btn btn-success',
								title: null,
								exportOptions: {
									columns: ':visible', // Solo exporta columnas visibles
									columns: ':not(.no-export)', // Excluye columnas con la clase no-export
									format: {
										header: function (data, columnIdx) {
											return data.trim(); // Limpia encabezados
										}
									}
								},
								customize: function (xlsx) {
									var sheet = xlsx.xl.worksheets['sheet1.xml'];
									var rows = $('row', sheet);

									// Inserta un encabezado para el campo tipo_transaccion_id
									var headerRow = rows.first();
									var headers = headerRow.find('c');
									headers.last().after('<c t="inlineStr"><is><t>Tipo Transacción ID</t></is></c>');

									// Añade tipo_transaccion_id a cada fila de datos
									rows.each(function (index) {
										if (index === 0) return; // Saltar encabezado

										// Obtener el valor de tipo_transaccion_id de los datos de la tabla
										var rowIndex = index - 1; // Ajustar índice porque el encabezado no cuenta
										var rowData = table.row(rowIndex).data(); // Obtener datos de la fila

										if (rowData) {
											var tipoTransaccionId = rowData.tipo_transaccion_id || ''; // Asegurarse de que exista
											console.log('tipoTransaccionId: ', tipoTransaccionId);
											// Agregar la celda con tipo_transaccion_id
											var lastCell = $(this).find('c').last();
											lastCell.after(`<c t="inlineStr"><is><t>${tipoTransaccionId}</t></is></c>`);		
											console.log('Sheet XML:', sheet);
										}
									});
								}
							},
							{
								text: '<i class="fas fa-file-import"></i> Importar desde Excel',
								className: 'btn btn-primary',
								action: function () {
									$('#importarExcel').click(); // Dispara el selector de archivo
								}
							}
						],
						order: [[0, 'asc']],
						initComplete: function() {
							console.log('Tabla inicializada correctamente.');
						}
					});
				},
				error: function(xhr) {
					console.error("Error al obtener los datos:", xhr.responseText);
				}
			});

			/*******************************************************************************************************************************
			 *******************************************************************************************************************************/
			document.getElementById('importarExcel').addEventListener('change', function (e) {
				const file = e.target.files[0];
				if (!file) return;

				const reader = new FileReader();

				reader.onload = function (event) {
					const data = new Uint8Array(event.target.result);
					const workbook = XLSX.read(data, { type: 'array' });

					// Leer la primera hoja del archivo
					const sheetName = workbook.SheetNames[0];
					const sheet = workbook.Sheets[sheetName];

					// Convertir el contenido de la hoja en un arreglo de objetos
					const jsonData = XLSX.utils.sheet_to_json(sheet, { defval: null }); // Asignar null a celdas vacías

					console.log('Datos importados desde Excel (sin procesar):', jsonData);

					// Agregar tipo_transaccion_id y procesar campos dinámicos
					const datosProcesados = jsonData.map((fila) => ({
						nombre: fila['Nombre'] || null,
						descripcion: fila['Descripción'] || null,
						tipo_transaccion_id: fila['tipo_transaccion_id'] || parseInt(fila['Tipo Transacción ID']) || null,
						transferencias: fila['Transferencias'] || null,
						asdf: fila['asdf'] || null
					}));

					console.log('Datos procesados para el servidor:', datosProcesados);
					enviarDatosAlServidor(datosProcesados);
					// Enviar los datos al servidor
					enviarDatosAlServidor(datosProcesados);
				};

				reader.readAsArrayBuffer(file);
			});

			/*******************************************************************************************************************************
			 *******************************************************************************************************************************/
			function enviarDatosAlServidor(datos) {
				console.log('Enviando datos al servidor:', datos);
				$.ajax({
					url: "{{ route('transacciones.importar') }}", // Ruta del servidor para procesar la importación
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Asegúrate de incluir el token CSRF
					},
					data: { datos: JSON.stringify(datos) },
					success: function (response) {
						console.log('Datos enviados con éxito:', response);
						// Recargar la tabla después de la importación
						$('#transacciones_table').DataTable().ajax.reload();
					},
					error: function (xhr) {
						console.error('Error al enviar los datos:', xhr.responseText);
					}
				});
			}

			/*******************************************************************************************************************************
			 *******************************************************************************************************************************/
			// Función para recargar la tabla
			function reload_table() {
				if (table && table.ajax) {
					table.ajax.reload(null, false); // Recargar sin cambiar de página
				} else {
					console.error('La tabla no está inicializada o la configuración de ajax es incorrecta.');
				}
			}


			/*******************************************************************************************************************************
			 *******************************************************************************************************************************/
			const agregarTransaccionBtn = $('#btn_agregar_transaccion');
			if (agregarTransaccionBtn.length > 0) {
				agregarTransaccionBtn.on('click', function() {
					$('#accion').val('add');
					$('#form')[0].reset();
					// Solicitar columnas al backend
					console.log('tipos_transacciones_id 1:', tipos_transacciones_id);
					$.ajax({
						url: "{{ url('transacciones/listado') }}" + '/' + tipos_transacciones_id,
						type: 'GET',
						headers: { 'Accept': 'application/json' },
						success: function(response) {
							console.log('Respuesta del servidor:', response);
							console.log('tipos_transacciones_id 2:', tipos_transacciones_id);
							if (response.columns) {
								abrirModalConCampos(response.columns); // Generar modal dinámico
							} else {
								console.error("No se recibieron columnas para mostrar en el modal");
							}
						},
						error: function(xhr) {
							console.error("Error al obtener los campos del modal:", xhr.responseText);
						}
					});
				});
			} else {
				console.error("El botón con ID 'btn_agregar_transaccion' no existe en el DOM.");
			}
		});

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
					console.log('Respuesta del servidor:', data);

					if (data && data.columns) {
						// Buscar el valor de 'id' en las columnas
						const idColumn = data.columns.find(col => col.nombre_campo === 'id');
						if (idColumn && idColumn.default_value) {
							$('#id').val(idColumn.default_value); // Asignar el valor al campo oculto
						} else {
							console.warn("No se encontró la columna 'id' en la respuesta.");
						}

						abrirModalConCampos(data.columns); // Generar modal dinámico con los campos formateados
					} else {
						console.error("No se recibieron columnas para mostrar en el modal");
					}
				},
				error: function(xhr) {
					console.error("Error al obtener los campos del modal:", xhr.responseText);
				}
			});
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function abrirModalConCampos(columns) {
			const formBody = document.querySelector('#modal_transacciones .form-body'); // Seleccionar contenedor del formulario

			// Limpiar campos dinámicos previamente añadidos
			formBody.innerHTML = '';

			// Iterar sobre las columnas y crear inputs dinámicos
			columns.forEach(column => {
				if (column.hidden) {
					// Crear un campo oculto
					const input = document.createElement('input');
					input.type = 'hidden';
					input.name = column.nombre_campo;
					input.id = column.nombre_campo;
					input.value = column.default_value || ''; // Usar el valor predeterminado
					formBody.appendChild(input);
					return; // No agregar al DOM como campo visible
				}

				// Crear campos visibles
				const fieldGroup = document.createElement('div');
				fieldGroup.classList.add('mb-3', 'row');

				// Etiqueta
				const label = document.createElement('label');
				label.classList.add('col-form-label', 'col-md-3');
				label.textContent = column.nombre_mostrar;

				// Contenedor del input
				const inputContainer = document.createElement('div');
				inputContainer.classList.add('col-md-9');

				let input;

				// Crear input dinámico según el tipo
				switch (column.tipo) {
					case '1': // Texto
						input = document.createElement('input');
						input.type = 'text';
						break;
					case '2': // Número
						input = document.createElement('input');
						input.type = 'number';
						break;
					case '3': // Fecha
						input = document.createElement('input');
						input.type = 'date';
						break;
					case '4': // Select
						input = document.createElement('select');
						input.classList.add('form-control');

						// Parsear los valores del JSON y agregar opciones al select
						if (column.valores) {
							try {
								const valores = typeof column.valores === 'string'
									? JSON.parse(column.valores) // Si es string, parsear
									: column.valores; // Si ya es un objeto, usarlo directamente

								Object.entries(valores).forEach(([key, value]) => {
									const option = document.createElement('option');
									option.value = key;
									option.textContent = value;

									// Seleccionar la opción correspondiente al valor del servidor
									if (key === column.default_value) {
										option.selected = true;
									}

									input.appendChild(option);
								});
							} catch (error) {
								console.error(`Error al parsear valores para la columna "${column.nombre_campo}":`, error);
							}
						}
						break;
					default: // Tipo por defecto (Texto)
						input = document.createElement('input');
						input.type = 'text';
				}

				// Aplicar propiedades comunes
				input.classList.add('form-control');
				input.name = column.nombre_campo;
				input.id = column.nombre_campo;
				input.placeholder = `Ingrese ${column.nombre_mostrar}`;
				input.required = true;

				if (column.tipo !== '4') {
					input.value = column.default_value || ''; // Solo para inputs que no sean select
				}

				// Agregar elementos al DOM
				inputContainer.appendChild(input);
				fieldGroup.appendChild(label);
				fieldGroup.appendChild(inputContainer);

				formBody.appendChild(fieldGroup);
			});

			// Abrir el modal después de actualizar los campos
			$('#modal_transacciones').modal('show');
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function guardar_datos() {
			let form_data = $('#form').serializeArray();
			console.log('form_data: ', form_data);
			console.log('tipos_transacciones_id 3:', tipos_transacciones_id);

			// Eliminar campos duplicados (mantener el último valor)
			const uniqueData = [];
			const fieldNames = new Set();

			for (let i = form_data.length - 1; i >= 0; i--) {
				if (!fieldNames.has(form_data[i].name)) {
					uniqueData.unshift(form_data[i]);
					fieldNames.add(form_data[i].name);
				}
			}

			form_data = uniqueData;

			console.log('Datos serializados únicos (antes de agregar tipo_transaccion_id):', form_data);
			// Agregar tipo_transaccion_id manualmente
			form_data.push({ name: 'tipo_transaccion_id', value: tipos_transacciones_id });
			console.log('Datos serializados únicos (después de agregar tipo_transaccion_id):', form_data);

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
				form_data.push({
					name: '_method',
					value: 'PUT'
				});
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
							$('#modal_transacciones').modal('hide'); // Cerrar el modal
							reload_table(); // Recargar la tabla
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
			console.log('validar formulario ');
			let nombre = document.querySelector('input[name="nombre"]').value.trim();
			let descripcion = document.querySelector('input[name="descripcion"]').value.trim();

			// Limpiar errores previos
			document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
			document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
			let errores = [];
			console.log('limpiar errores ');

			// Validación del campo 'nombre'
			if (!nombre) errores.push({
				campo: 'nombre',
				mensaje: "El campo 'Nombre' es obligatorio."
			});
			if (nombre.length > 100) errores.push({
				campo: 'nombre',
				mensaje: "El campo 'Nombre' no debe exceder los 100 caracteres."
			});
			if (nombre.length < 3) errores.push({
				campo: 'nombre',
				mensaje: "El campo 'Nombre' no debe tener menos de 3 caracteres."
			});
			if (!/^[a-zA-Z0-9\s]+$/.test(nombre)) {
				errores.push({
					campo: 'nombre',
					mensaje: "El campo 'Nombre' sólo puede contener letras, números y espacios."
				});
			}

			// Validación del campo 'descripcion'
			//if (!descripcion) errores.push({ campo: 'descripcion', mensaje: "El campo 'Descripción' es obligatorio." });
			if (descripcion.length > 100) errores.push({
				campo: 'descripcion',
				mensaje: "El campo 'Descripcion' no debe exceder los 255 caracteres."
			});
			if (descripcion.length < 3) errores.push({
				campo: 'descripcion',
				mensaje: "El campo 'Descripcion' no debe tener menos de 3 caracteres."
			});

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
			if (table && table.ajax) {
				table.ajax.reload(null, false); // Recargar sin cambiar de página
			} else {
				console.error('La tabla no está inicializada o la configuración de ajax es incorrecta.');
			}
		}

/* 		/*******************************************************************************************************************************
		 *******************************************************************************************************************************
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
 */

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
		<?php 
		#dd($id);
		$tipo_transaccion_nombre = TipoTransaccion::find($id)->nombre;
		?>
		<div class="row">
			<div class="col-md-12">
				<h2>Transacciones - {{ $tipo_transaccion_nombre }}</h2>
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
						<!--button id="agregar" class="btn btn-success mr-2" onclick="add_transacciones()"-->
						<button id="btn_agregar_transaccion" class="btn btn-success mr-2" >
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
								<path d="M8 4v8m4-4H4" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
							</svg> {{ __('Agregar Transacción') }}
						</button>
					</div>

					<table id="transacciones_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
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

	<div class="modal fade" id="modal_transacciones" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Transacciones - {{ $tipo_transaccion_nombre }}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
					@csrf
					<input name="_method" type="hidden" id="method">
					<div class="modal-body form">
						<input type="hidden" value="" name="id" />
						<input name="accion" id="accion" class="form-control" type="hidden">
						<input type="file" id="importarExcel" style="display: none;" accept=".xlsx,.xls" />
						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">{{ __('Nombre') }}</label>
								<div class="col-md-9">
									<input name="nombre" minlength="3" maxlength="100" placeholder="Nombre de la Transacción"
										id="nombre" class="form-control" type="text" required>
									<span class="help-block"></span>
								</div>
							</div>
						</div>
						<?php #dd($transacciones); 
						?>
						<!-- Contenedor de campos dinámicos -->
						<div class="form-body"></div>

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