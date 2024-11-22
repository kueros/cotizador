	<x-app-layout title="Roles" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
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

			jQuery(document).ready(function($){
			/*******************************************************************************************************************************
			 *******************************************************************************************************************************/
				table = $('#funciones-table').DataTable({
					"ajax": {
						url : "{{ url('funciones/ajax_listado') }}",
						type : 'GET'
					},
					language: traduccion_datatable,
					dom: 'Bfrtip',
					columnDefs: [
						{
							"targets": 'no-sort',
							"orderable": false
						}
					],
					buttons: [
						{"extend": 'pdf', "text":'Export',"className": 'btn btn-danger', "orientation": 'landscape', title: 'Funciones'},
						{"extend": 'copy', "text":'Export',"className": 'btn btn-primary', title: 'Funciones'},
						{"extend": 'excel', "text":'Export',"className": 'btn btn-success', title: 'Funciones'},
						{"extend": 'print', "text":'Export',"className": 'btn btn-secondary', title: 'Funciones'}
					],
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
			function reload_table()
			{
				table.ajax.reload(null,false);
			}

			/*******************************************************************************************************************************
			 *******************************************************************************************************************************/
			function add_funcion()
			{
				save_method = 'add';
				$('#form_add')[0].reset();
				$('.form-group').removeClass('has-error');
				$('.help-block').empty();
				$('#modal_form_add').modal('show');
				$('.modal-title').text('Agregar funciones');
				$('#accion').val('add');
				$('#form_add').attr('action', "{{ url('funciones') }}");
				$('#method').val('POST');
			}

			/*******************************************************************************************************************************
			 *******************************************************************************************************************************/
			function edit_funcion(id)
			{
				save_method = 'update';
				$('#form_edit')[0].reset();
				$('.form-group').removeClass('has-error');
				$('.help-block').empty();
				$('#accion').val('edit');
				$.ajax({
					url : "{{ url('funciones/ajax_edit/') }}" + "/" + id,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{
						$('[name="id"]').val(data.id);
						$('[name="nombre"]').val(data.nombre);

						<?php //if($utilizar_id_grupo){
						?>
						$('[name="id_grupo"]').val(data.id_grupo);
						<?php
						//}?>

						$('#modal_form_edit').modal('show');
						$('.modal-title').text('Editar funcion');
						$('#form_edit').attr('action', "{{ url('funciones') }}" + "/" + id);
						$('#method').val('PUT');
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});
			}

			/*******************************************************************************************************************************
			 *******************************************************************************************************************************/
			function delete_funcion(id)
			{
				if(confirm('¿Desea borrar la función?'))
				{
					$.ajax({
						url : "{{ url('funciones/ajax_delete') }}"+"/"+id,
						type: "POST",
						dataType: "JSON",
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						success: function(data)
						{
							swal.fire("Aviso", "Función eliminada con éxito.", "success");
							$('#modal_form_edit').modal('hide');
							$('#modal_form_add').modal('hide');
							reload_table();
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							show_ajax_error_message(jqXHR, textStatus, errorThrown);
						}
					});

				}
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
			document.addEventListener('DOMContentLoaded', function () {
				// Array para almacenar la fórmula
				//let formulaElements = [];
				document.getElementById('form_add').addEventListener('submit', function (event) {
					// Evita el envío automático para procesar los datos
					event.preventDefault();

					// Recopilar todos los inputs del contenedor
					const inputs = document.querySelectorAll('#input-container input');
					let formulaString = '';

					// Concatenar los valores de cada input al string
					inputs.forEach(input => {
						if (input.value.trim() !== '') {
							formulaString += input.value.trim();
						}
					});

					// Asignar el string concatenado al campo oculto
					document.getElementById('formula').value = formulaString;

					// Enviar el formulario
					this.submit();
				});
			});

			// Función para agregar elementos a la fórmula
			function addOption(optionText) {
				let formulaElements = [];
				let addedElements = []; 
				const container = document.getElementById('input-container');

				const wrapper = document.createElement('div');
				wrapper.className = 'd-flex align-items-center me-2';

				const newInput = document.createElement('input');
				newInput.type = 'number';
				newInput.className = 'form-control';
				newInput.style.width = '100px';


				if (optionText === 'Valor numérico') {
					newInput.placeholder = 'Ingrese un valor';
					//newInput.type = 'number';

					// Crear un objeto asociado en formulaElements
					const elementIndex = formulaElements.length;
					formulaElements.push({ type: 'value', value: '' }); // Inicializar en vacío

					// Actualizar el valor en formulaElements cuando se cambie el input
					newInput.oninput = function () {
						formulaElements[elementIndex].value = newInput.value;
						console.log(formulaElements) 
					};
					newInput.removeAttribute('readonly');
				} else if (optionText === 'Operador') {
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
							formulaElements.push({ type: 'operator', value: op });
						};
						li.appendChild(button);
						dropdownMenu.appendChild(li);
					});

					operatorDropdown.appendChild(dropdownButton);
					operatorDropdown.appendChild(dropdownMenu);

					wrapper.appendChild(operatorDropdown);
				} else {
					newInput.value = optionText;
					//newInput.setAttribute('readonly', false); // Esto asegura que sea editable
					formulaElements.push({ type: 'static', value: optionText });
				}

				wrapper.appendChild(newInput);
				container.insertBefore(wrapper, container.lastElementChild);
				serializeFormula();
			}

			// Función para serializar la fórmula
			function serializeFormula() {
				const serializedFormula = JSON.stringify(formulaElements);
				document.getElementById('formula').value = serializedFormula;
			}

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
								<i class="bi bi-plus"></i> {{ __('Agregar funcion') }}
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
		<div class="modal fade modal-lg" id="modal_form_add" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Formulario funciones</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="form_add" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
					@csrf
					<input name="_method" type="hidden" id="method">
					<input type="hidden" id="formula" name="formula">
					<div class="modal-body form">
						<input type="hidden" value="" name="id"/>
						<input name="accion" id="accion" class="form-control" type="hidden">
						<div class="form-body">
							<div class="row">
								<div class="col-md-3">&nbsp;</div>
								<label class="col-form-label col-md-3">Nombre</label>
								<div class="col-md-6">
									<input name="nombre" maxlength="255" placeholder="Nombre de la función" class="form-control" type="text" />
									<span class="help-block"></span>
								</div>
							</div>
						</div>
						<div class="form-body">
							<div class="row align-items-center">
								<div class="col-md-3 d-flex align-items-center">
									<input name="segundo_nombre" maxlength="255" placeholder="Nombre de la función" class="form-control" type="text" readonly="readonly" />
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
											<li><button class="dropdown-item" type="button" onclick="addOption('1')">Valor numérico</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Operador')">Operador</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Contador')">Contador</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Acumulador')">Acumulador</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Condición')">Condición</button></li>
											<li><button class="dropdown-item" type="button" onclick="addOption('Otra función')">Otra función</button></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary">Guardar</button>
							<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
			<div class="modal fade modal-lg" id="modal_form_edit" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Formulario funciones</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<form id="form_edit" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
						@csrf
						<input name="_method" type="hidden" id="method">
						<input type="hidden" id="formula" name="formula">
						<div class="modal-body form">					
							<input type="hidden" value="" name="id"/>
							<input name="accion" id="accion" class="form-control" type="hidden">
							<div class="form-body">
								<div class="row">
									<div class="col-md-3">&nbsp;</div>
									<label class="col-form-label col-md-3">Nombre</label>
										<div class="col-md-6">
											<input name="nombre" maxlength="255" placeholder="Nombre de la función" class="form-control" type="text" />
											<span class="help-block"></span>
										</div>
									</div>
								</div>
							</div>
							<div class="form-body">
								<div class="row">
									<div class="col-md-3">
										<input name="segundo_nombre" maxlength="255" placeholder="Nombre de la función" class="form-control" type="text" readonly="readonly" />
										<span class="help-block"></span>
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
