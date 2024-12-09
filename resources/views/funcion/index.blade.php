<x-app-layout title="Funciones" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Funciones') }}
		</h2>
	</x-slot>
	<!-- Primary Navigation Menu -->
	<!--	@php
		$user = Auth::user()->username;
		$email = Auth::user()->email;
		$permiso_agregar_roles = tiene_permiso('add_rol');
		$permiso_editar_roles = tiene_permiso('edit_rol');
		$permiso_eliminar_roles = tiene_permiso('del_rol');
		@endphp
	-->
	<script type="text/javascript">
		var table;
		var save_method;
		jQuery(document).ready(function($) {
			table = $('#funciones-table').DataTable({
				"ajax": {
					url: "{{ url('funciones/ajax_listado') }}",
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
		function add_funcion() {
			save_method = 'add';
			$('#form_add')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#modal_form_add').modal('show');
			$('.modal-title').text('Agregar función');
			$('#accion').val('add');
			$('#form_add').attr('action', "{{ url('funciones') }}");
			$('#method').val('POST');
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function edit_funcion(id) {
			save_method = 'update';
			$('#form_add')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');

			$.ajax({
				url: "{{ url('funciones/ajax_edit/') }}" + "/" + id,
				type: "GET",
				dataType: "JSON",
				success: function(data) {
					console.log('Respuesta completa del servidor:', data);
					if (typeof data === 'string') {
						try {
							data = JSON.parse(data);
						} catch (e) {
							console.error('Error al parsear JSON:', e);
							return;
						}
					}
					if (data && data.formula) {
						if (Array.isArray(data.formula)) {
							data.formula.forEach(function(item) {
								console.log(item);
							});
						} else {
							console.error("La propiedad 'formula' no es un array.");
						}
					} else {
						console.error("La propiedad 'formula' no existe en la respuesta.");
					}
					$('[name="id"]').val(data.id);
					$('[name="nombre"]').val(data.nombre);
					clearDynamicFields();
					// Genera los campos dinámicos según la fórmula
					if (Array.isArray(data.formula)) {
						data.formula.forEach(element => {
							console.log('element: ', element);
							if (element.type === 'valor') {
								addOption('Valor numérico', element.value);
							} else if (element.type === 'operador') {
								addOption('Operador', element.value);
							} else if (element.type === 'contador') {
								addOption('Contador', element.value);
							} else if (element.type === 'acumulador') {
								addOption('Acumulador', element.value);
							} else if (element.type === 'condicion') {
								console.log('Condicion: ', element.value);
								addOption('Condicion', element.value);
							} else if (element.type === 'tipo') {
								addOption('Tipo', element.value);
							} else if (element.type === 'campo') {
								addOption('Campo', element.value);
							} else if (element.type === 'funcion') {
								addOption('Funcion', element.value);
							} else {
								addOption('Campo estático', element.value);
							}
						});
					}

/* 					<li><button class="dropdown-item" type="button" onclick="addOption('Value')">Valor numérico</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Operador')">Operador</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Contador')">Contador</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Acumulador')">Acumulador</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Condición')">Condición</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Tipos de Transacciones')">Tipos de Transacciones</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Campos de Transacciones')">Campos de Transacciones</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Otra función')">Otra función</button></li>
 */


					$('#modal_form_add').modal('show');
					$('.modal-title').text('Editar función');
					$('#form').attr('action', "{{ url('funciones') }}" + "/" + id);
					$('#method').val('PUT');
				},
				error: function(jqXHR, textStatus, errorThrown) {
					show_ajax_error_message(jqXHR, textStatus, errorThrown);
				}
			});
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function delete_funcion(id) {
			if (confirm('¿Desea borrar la función?')) {
				$.ajax({
					url: "{{ url('funciones/ajax_delete') }}" + "/" + id,
					type: "POST",
					dataType: "JSON",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data) {
						swal.fire("Aviso", "Función eliminada con éxito.", "success");
						//$('#modal_form_edit').modal('hide');
						$('#modal_form_add').modal('hide');
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
			if (!cargarFormula()) {
				return false;
			}
			let form_data = $('#form_add').serializeArray();
			let url_guarda_datos = "{{ url('funciones/ajax_store') }}";
			let type_guarda_datos = "POST";

			if ($('#accion').val() != "add") {
				url_guarda_datos = "{{ url('funcionesUpdate') }}" + "/" + 2;
				type_guarda_datos = "PUT";
				form_data.push({
					name: '_method',
					value: 'PUT'
				});
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
									errorMessage += `${field}: ${data.errors[field].join(", ")}<br/>`;
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
							$('#modal_form_add').modal('hide');
							reload_table();
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
		document.addEventListener('DOMContentLoaded', function() {
			const firstInput = document.querySelector('input[name="nombre"]');
			const secondInput = document.querySelector('input[name="segundo_nombre"]');

			firstInput.addEventListener('input', function() {
				secondInput.value = firstInput.value;
			});
		});

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		// Array para almacenar la fórmula
		let formulaElements = [];

		function cargarFormula() {
			console.log('Cargando fórmula...');
			// Array para almacenar la fórmula
			//let formulaElements = [];
			//document.getElementById('form_add').addEventListener('submit', function (event) {
			console.log('Formulario enviado');
			// Evita el envío automático para procesar los datos
			event.preventDefault();

			// Recopilar todos los inputs del contenedor
			const inputs = document.querySelectorAll('#input-container input');
			let formulaString = '';

			// Concatenar los valores de cada input al string
			inputs.forEach(input => {
				if (input.value.trim() !== '') {
					formulaString += input.value.trim() + ',';
				}
			});
			console.log('formulaString: ', formulaString);
			// Asignar el string concatenado al campo oculto
			document.getElementById('formula').value = formulaString;

			// Enviar el formulario
			//this.submit();
			//});
			return true
		};

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		// Función para agregar elementos a la fórmula
		let tipoSeleccionado = null; // Variable para almacenar el tipo de transacciones seleccionado
		let campoSeleccionado = null; // Variable para almacenar el campo de transacciones seleccionado
		let contadorNodos = 0;
		function addOption(optionText, defaultValue = '') {
			let formulaElements = [];
			let addedElements = [];
			const container = document.getElementById('input-container');

			const wrapper = document.createElement('div');
			wrapper.className = 'd-flex align-items-center me-2';

			const newInput = document.createElement('input');
			newInput.type = 'number';
			newInput.className = 'form-control';
			newInput.style.width = '100px';

			// Aviso de error
			const avisoError = document.getElementById('aviso-error');
			if (!avisoError) {
				const errorDiv = document.createElement('div');
				errorDiv.id = 'aviso-error';
				errorDiv.style.color = 'red';
				errorDiv.style.display = 'none';
				container.appendChild(errorDiv);
			}

			/*******************************************************************************************************************************/
			if (optionText === 'Valor numérico') {
				console.log('Valor numérico seleccionado:', optionText);
				newInput.type = 'number';
				newInput.value = defaultValue;

				const elementIndex = formulaElements.length;
				formulaElements.push({
					type: 'valor',
					value: ''
				});

				newInput.oninput = function() {
					formulaElements[elementIndex].value = newInput.value;
					console.log(formulaElements);
				};
				newInput.removeAttribute('readonly');
			/*******************************************************************************************************************************/
			} else if (optionText === 'Operador') {
				console.log('Operador seleccionado:', optionText);
				// Lógica para el operador
				newInput.setAttribute('readonly', true);
				newInput.type = 'text';
				const operatorDropdown = document.createElement('div');
				operatorDropdown.className = 'dropdown ms-2';

				const dropdownButton = document.createElement('button');
				dropdownButton.className = 'btn btn-primary dropdown-toggle';
				dropdownButton.type = 'button';
				dropdownButton.id = `dropdownOperator-${Date.now()}`;
				dropdownButton.setAttribute('data-bs-toggle', 'dropdown');
				dropdownButton.innerText = 'Seleccionar operador';

				const dropdownMenu = document.createElement('ul');
				dropdownMenu.className = 'dropdown-menu';
				dropdownMenu.setAttribute('aria-labelledby', dropdownButton.id);

				const operators = ['+', '-', '*', '/'];
				operators.forEach(op => {
					const li = document.createElement('li');
					const button = document.createElement('button');
					button.className = 'dropdown-item';
					button.type = 'button';
					button.innerText = op;
					button.onclick = () => {
						newInput.value = op;
						operatorDropdown.style.display = 'none';
						formulaElements.push({
							type: 'operador',
							value: op
						});
					};
					li.appendChild(button);
					dropdownMenu.appendChild(li);
				});

				operatorDropdown.appendChild(dropdownButton);
				operatorDropdown.appendChild(dropdownMenu);
				wrapper.appendChild(operatorDropdown);
			/*******************************************************************************************************************************/
			} else if (optionText === 'Contador') {
				console.log('Contador seleccionado:', optionText);
				newInput.setAttribute('readonly', true);

				// Verificar si se ha elegido un campo de transacciones
/* 				if (!campoSeleccionado) {
					const avisoError = document.getElementById('aviso-error');
					avisoError.style.display = 'block';
					avisoError.innerText = '¡Por favor, elija primero un campo de transacciones antes de seleccionar un contador!';
					return; // Detener la ejecución si no se ha seleccionado un campo de transacciones
				}
*/

					// Lógica para el contador
					fetch('/funciones/contar-transacciones', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // Si usas CSRF tokens
						},
						body: JSON.stringify({
							nombre_mostrar: campoSeleccionado
						}) // Enviar el campo seleccionado
					})
					.then(response => response.json())
					.then(data => {
						if (data.error) {
							const avisoError = document.getElementById('aviso-error');
							avisoError.style.display = 'block';
							avisoError.innerText = data.error;
						} else {
							newInput.value = data.contador; // Actualizar el valor del input
							const elementIndex = formulaElements.length;
							formulaElements.push({
								type: 'contador',
								value: data.contador
							});
							console.log(formulaElements);
						}
					})
					.catch(error => {
						console.error('Error al contar transacciones:', error);
					});

			/*******************************************************************************************************************************/
			} else if (optionText === 'Acumulador') {
				console.log('Acumulador seleccionado:', optionText);
				newInput.setAttribute('readonly', true);

				// Verificar si se ha elegido un campo de transacciones
/* 				if (!campoSeleccionado) {
					const avisoError = document.getElementById('aviso-error');
					avisoError.style.display = 'block';
					avisoError.innerText = '¡Por favor, elija primero un campo de transacciones antes de seleccionar un acumulador!';
					return; // Detener la ejecución si no se ha seleccionado un campo de transacciones
				} */
					// Lógica para el acumulador
					fetch('/funciones/acumular-transacciones', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // Si usas CSRF tokens
						},
						body: JSON.stringify({
							nombre_mostrar: campoSeleccionado
						}) // Enviar el campo seleccionado
					})
					.then(response => response.json())
					.then(data => {
						if (data.error) {
							const avisoError = document.getElementById('aviso-error');
							avisoError.style.display = 'block';
							avisoError.innerText = data.error;
						} else {
							newInput.value = data.acumulador; // Actualizar el valor del input
							const elementIndex = formulaElements.length;
							formulaElements.push({
								type: 'acumulador',
								value: data.acumulador
							});
							console.log(formulaElements);
						}
					})
					.catch(error => {
						console.error('Error al acumular transacciones:', error);
					});


			/*******************************************************************************************************************************/
			} else if (optionText === 'Otra funcion') {
				console.log('Otra función seleccionada:', optionText);
				newInput.setAttribute('readonly', true);
				newInput.type = 'text';
				
				const avisoError = document.getElementById('aviso-error');
				avisoError.style.display = 'none';
				avisoError.innerText = '';

				const dropdownContainer = document.createElement('div');
				dropdownContainer.className = 'dropdown ms-2';

				const dropdownButton = document.createElement('button');
				dropdownButton.className = 'btn btn-primary dropdown-toggle';
				dropdownButton.type = 'button';
				dropdownButton.id = `dropdownFunciones-${Date.now()}`;
				dropdownButton.setAttribute('data-bs-toggle', 'dropdown');
				dropdownButton.innerText = 'Seleccionar función';

				const dropdownMenu = document.createElement('ul');
				dropdownMenu.className = 'dropdown-menu';
				dropdownMenu.setAttribute('aria-labelledby', dropdownButton.id);

				// Hacer una solicitud al backend para obtener las funciones
				fetch('/funciones/listado')
					.then(response => response.json())
					.then(funciones => {
						funciones.forEach(funcion => {
							const li = document.createElement('li');
							const button = document.createElement('button');
							button.className = 'dropdown-item';
							button.type = 'button';
							button.innerText = funcion.nombre; // Campo 'nombre' recibido desde el backend
							button.onclick = () => {
								newInput.value = funcion.nombre;
								formulaElements.push({ type: 'funcion', value: funcion.nombre });
								dropdownContainer.style.display = 'none';
							};
							li.appendChild(button);
							dropdownMenu.appendChild(li);
						});
					})
					.catch(error => console.error('Error al cargar funciones:', error));

				dropdownContainer.appendChild(dropdownButton);
				dropdownContainer.appendChild(dropdownMenu);

				wrapper.appendChild(dropdownContainer);

			/*******************************************************************************************************************************/
			} else if (optionText === 'Condicion') {
				console.log('Condición seleccionada:', optionText);
				// Lógica para la condición
				newInput.setAttribute('readonly', true);
				newInput.type = 'text';
				const operatorDropdown = document.createElement('div');
				operatorDropdown.className = 'dropdown ms-2';

				const dropdownButton = document.createElement('button');
				dropdownButton.className = 'btn btn-primary dropdown-toggle';
				dropdownButton.type = 'button';
				dropdownButton.id = `dropdownOperator-${Date.now()}`;
				dropdownButton.setAttribute('data-bs-toggle', 'dropdown');
				dropdownButton.innerText = 'Seleccionar condición';

				const dropdownMenu = document.createElement('ul');
				dropdownMenu.className = 'dropdown-menu';
				dropdownMenu.setAttribute('aria-labelledby', dropdownButton.id);

				const operators = ['=', '<', '>', 'SI', 'AND', 'OR', 'NOT', '<=', '>=', '!='];
				operators.forEach(op => {
					const li = document.createElement('li');
					const button = document.createElement('button');
					button.className = 'dropdown-item';
					button.type = 'button';
					button.innerText = op;
					button.onclick = () => {
						newInput.value = op;
						operatorDropdown.style.display = 'none';
						formulaElements.push({
							type: 'condicion',
							value: op
						});
					};
					li.appendChild(button);
					dropdownMenu.appendChild(li);
				});

				operatorDropdown.appendChild(dropdownButton);
				operatorDropdown.appendChild(dropdownMenu);
				wrapper.appendChild(operatorDropdown);

			/*******************************************************************************************************************************/

			} else if (optionText.trim().toLowerCase() === 'tipo') {
				console.log('Tipos de transacciones seleccionados:', optionText);
				newInput.setAttribute('readonly', true);
				newInput.type = 'text';
				const avisoError = document.getElementById('aviso-error');
				avisoError.style.display = 'none';
				avisoError.innerText = '';

				const dropdownContainer = document.createElement('div');
				dropdownContainer.className = 'dropdown ms-2';

				const dropdownButton = document.createElement('button');
				dropdownButton.className = 'btn btn-primary dropdown-toggle';
				dropdownButton.type = 'button';
				dropdownButton.id = `dropdownTipos-${Date.now()}`;
				dropdownButton.setAttribute('data-bs-toggle', 'dropdown');
				dropdownButton.innerText = 'Seleccionar tipo';

/*

VERIFICAR SI ESTOY EN EDICION O CREACION DE FUNCIONES
				LUEGO DE ESO, SI ESTOY EN CREACION, LA LINEA ANTERIOR ESTÁ BIEN, PERO SI ESTOY EN EDICION
				EN LUGAR DE SELECCIONAR TIPO DEBERÍA IR EL VALOR QUE TRAE DESDE LA TABLA.

*/


				const dropdownMenu = document.createElement('ul');
				dropdownMenu.className = 'dropdown-menu';
				dropdownMenu.setAttribute('aria-labelledby', dropdownButton.id);

				// Hacer una solicitud al backend para obtener los tipos

				fetch('/funciones/tipos_transacciones')
						.then(response => response.json())
						.then(tipos => {
							console.log('Datos recibidos del backend:', tipos); // Verifica la respuesta
							tipos.forEach(tipo => {
								const li = document.createElement('li');
								const button = document.createElement('button');
								button.className = 'dropdown-item';
								button.type = 'button';
								button.innerText = tipo.nombre; // Campo 'nombre' recibido desde el backend
								button.onclick = () => {
									newInput.value = tipo.nombre;
									formulaElements.push({ type: 'tipo', value: tipo.nombre });
									dropdownContainer.style.display = 'none';
									tipoSeleccionado = tipo.nombre; // Guardar el campo seleccionado
								};
								li.appendChild(button);
								dropdownMenu.appendChild(li);
							});
						})
					.catch(error => console.error('Error al cargar tipos:', error));

				dropdownContainer.appendChild(dropdownButton);
				dropdownContainer.appendChild(dropdownMenu);

				wrapper.appendChild(dropdownContainer);

			/*******************************************************************************************************************************/
			} else if (optionText === 'campo') {
				console.log('Campos de transacciones seleccionados:', optionText);
				newInput.setAttribute('readonly', true);
				newInput.type = 'text';
				const avisoError = document.getElementById('aviso-error');
				avisoError.style.display = 'none';
				avisoError.innerText = '';

				console.log('tipoSeleccionado: ', tipoSeleccionado);

				// Verificar si se ha elegido un tipo de transacciones
				if (!tipoSeleccionado) {
					avisoError.style.display = 'block';
					avisoError.innerText = '¡Por favor, elija primero un tipo de transacciones antes de seleccionar los campos!';
					return; // Detener la ejecución si no se ha seleccionado un tipo de transacciones
				}

				const dropdownContainer = document.createElement('div');
				dropdownContainer.className = 'dropdown ms-2';

				const dropdownButton = document.createElement('button');
				dropdownButton.className = 'btn btn-primary dropdown-toggle';
				dropdownButton.type = 'button';
				dropdownButton.id = `dropdownCampos-${Date.now()}`;
				dropdownButton.setAttribute('data-bs-toggle', 'dropdown');
				dropdownButton.innerText = 'Seleccionar campo';

				const dropdownMenu = document.createElement('ul');
				dropdownMenu.className = 'dropdown-menu';
				dropdownMenu.setAttribute('aria-labelledby', dropdownButton.id);

				// Hacer una solicitud al backend para obtener los campos relacionados al tipo seleccionado
				fetch('/funciones/campos-transacciones', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
						},
						body: JSON.stringify({ tipo_transaccion: tipoSeleccionado })
					})
					.then(response => response.json())
					.then(campos => {
						console.log('Datos recibidos del backend:', campos); // Verifica la respuesta

						if (campos.error) {
							avisoError.style.display = 'block';
							avisoError.innerText = campos.error;
							return;
						}

						if (!campos || campos.length === 0) {
							avisoError.style.display = 'block';
							avisoError.innerText = 'No se encontraron campos disponibles.';
							return;
						}

						campos.forEach(campo => {
							console.log('Campo procesado:', campo); // Verifica cada elemento

							const li = document.createElement('li');
							const button = document.createElement('button');
							button.className = 'dropdown-item';
							button.type = 'button';
							button.innerText = campo; // Ya que el backend devuelve un string
							button.onclick = () => {
								newInput.value = campo; // Ajusta según el backend
								campoSeleccionado = campo; // Guarda el valor seleccionado
								dropdownContainer.style.display = 'none';
							};

							li.appendChild(button);
							dropdownMenu.appendChild(li);
						});

					})
					.catch(error => {
						console.error('Error al cargar campos:', error);
				});


				dropdownContainer.appendChild(dropdownButton);
				dropdownContainer.appendChild(dropdownMenu);

				wrapper.appendChild(dropdownContainer);

			/*******************************************************************************************************************************/
			} else {
				console.log('Campo estático seleccionado:', optionText);
				newInput.value = optionText;
				formulaElements.push({
					type: 'static',
					value: optionText
				});
			}
			
			wrapper.appendChild(newInput);

			if (contadorNodos == 0) {
				container.insertBefore(wrapper, container.childNodes[0]);
				contadorNodos = 1
			} else {
				container.insertBefore(wrapper, container.childNodes[contadorNodos]);
				contadorNodos = contadorNodos + 1
			}

			serializeFormula();
		}


		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		// Función para serializar la fórmula
		function serializeFormula() {
			const serializedFormula = JSON.stringify(formulaElements);
			document.getElementById('formula').value = serializedFormula;
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function backInput() {
			const container = document.getElementById('input-container');

			// Selecciona todos los divs con las clases específicas
			const inputDivs = container.querySelectorAll('div.d-flex.align-items-center.me-2');
			console.log(inputDivs);
			// Verifica si hay elementos
			if (inputDivs.length > 0) {
				// Seleccionamos el último div con el input
				const lastInputDiv = inputDivs[inputDivs.length - 1];

				// Lo eliminamos del contenedor
				lastInputDiv.remove();

				// También eliminamos el último elemento del array formulaElements
				formulaElements.pop();
				serializeFormula(); // Actualizamos el campo de fórmula serializada
			} else {
				console.warn('No hay elementos para eliminar');
			}
		}

		/*******************************************************************************************************************************
		 *******************************************************************************************************************************/
		function clearDynamicFields() {
			// Selecciona el contenedor de los campos dinámicos
			const container = document.getElementById('input-container');

			// Selecciona todos los divs con las clases específicas dentro del contenedor
			const inputDivs = container.querySelectorAll('div.d-flex.align-items-center.me-2');

			// Itera y elimina cada elemento dinámico
			inputDivs.forEach(div => div.remove());

			// Limpia el array formulaElements si es necesario
			formulaElements = [];
			serializeFormula(); // Actualiza el campo de fórmula serializada

			// Cierra la modal manualmente en caso de que `data-bs-dismiss="modal"` no funcione
			const modal = document.getElementById('modal_form_add');
			if (modal) {
				const bootstrapModal = bootstrap.Modal.getInstance(modal);
				if (bootstrapModal) {
					bootstrapModal.hide();
				}
			}
		}
	</script>
	<style>
		.modal-body .form-body .row {
			align-items: center;
		}

		.modal-body .form-body .d-flex {
			display: flex;
			align-items: center;
		}

		.modal-body .form-body .ms-3 {
			margin-left: 1rem;
		}

		.btn-sm {
			padding: 0.25rem 0.5rem;
			font-size: 0.875rem;
			line-height: 1.5;
			border-radius: 0.2rem;
		}

		#input-container {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			/* Espaciado entre elementos */
			max-width: 100%;
			/* Limita el ancho al del modal */
		}
	</style>

	<!--LISTADO-->
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h2>Funciones</h2>
				@include('layouts.partials.message')
				@if ($errors->any())
				<div class="alert alert-danger">
					@foreach ($errors->all() as $error)
					{{ $error }}</br>
					@endforeach
				</div>
				@endif
				<br>
				<div class="table-responsive">
					<div class="float-right">
						<button id="agregar" class="btn btn-success" onclick="add_funcion()">
							<i class="bi bi-plus"></i> {{ __('Agregar función') }}
						</button>
					</div>
					<br>
					<table id="funciones-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<th>Nombre</th>
							<th>Fórmula</th>
							<th style="width:20%;" class="no-sort">Acción</th>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
	<div class="modal fade " id="modal_form_add" role="dialog">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Formulario funciones</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="form_add" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
					@csrf
					<input name="_method" type="hidden" id="method">
					<input type="hidden" id="formula" name="formula">
					<div class="modal-body form" style="overflow-y:visible;">
					<input type="hidden" value="" name="id" />
						<input name="accion" id="accion" class="form-control" type="hidden">
						<div class="form-body">
							<div class="row">
								<div class="col-md-4">&nbsp;</div>
								<label class="col-form-label col-md-1">Nombre</label>
								<div class="col-md-7">
									<input name="nombre" maxlength="255" placeholder="Nombre de la función" class="form-control" type="text" />
									<span class="help-block"></span>
								</div>
							</div>
						</div>
						<hr>
						<div class="form-body">
							<div class="row align-items-center">
								<div class="col-md-3 d-flex align-items-center">
									<input name="segundo_nombre" maxlength="255" class="form-control" type="text" readonly="readonly" />
									<button class="btn btn-info btn-sm ms-2" type="button" id="equalMenuButton" disabled>
										<i class="fas fa-equals"></i>
									</button>
									<span class="help-block"></span>
								</div>
								<!-- Mueve el bloque input-container aquí -->
								<div class="col-md-9 d-flex align-items-center" id="input-container">
									<!-- Dropdown -->
									<div class="dropdown">
										<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="far fa-plus-square"></i>
										</button>
										<button class="btn btn-secondary" type="button" id="backMenuButton" onclick="backInput()">
											<i class="fas fa-undo"></i>
										</button>
										<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<li><button class="dropdown-item" type="button" onclick="addOption('Value')">Valor numérico</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Operador')">Operador</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Contador')">Contador</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Acumulador')">Acumulador</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Condición')">Condición</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Tipos de Transacciones')">Tipos de Transacciones</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Campos de Transacciones')">Campos de Transacciones</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Otra función')">Otra función</button></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<!--a onclick="if (validarFormulario()) guardar_datos();" class="btn btn-primary">Guardar</a-->
							<a onclick=" guardar_datos();" class="btn btn-primary">Guardar</a>
							<button type="button" class="btn btn-danger" onclick="clearDynamicFields()">Cancelar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

</x-app-layout>