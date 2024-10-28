<x-app-layout title="Roles" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Roles') }}
		</h2>
	</x-slot>

	<script type="text/javascript">
		var table;
		var save_method;

		jQuery(document).ready(function($){
			$('#roles-table').DataTable({dom: 'Bfrtip',
				buttons: [
					{"extend": 'pdf', "text":'Export',"className": 'btn btn-danger', "orientation": 'landscape', title: 'Roles'},
					{"extend": 'copy', "text":'Export',"className": 'btn btn-primary', title: 'Roles'},
					{"extend": 'excel', "text":'Export',"className": 'btn btn-success', title: 'Roles'},
					{"extend": 'print', "text":'Export',"className": 'btn btn-secondary', title: 'Roles'}
				],
				initComplete: function () {
					$('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
					$('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
					$('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
					$('.buttons-print').html('<span class="glyphicon glyphicon-print" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir');
				}
			})
			/*table = $('#roles-table').DataTable({
				"ajax": {
					url : "{{ url('roles/ajax_listado') }}",
					type : 'GET'
				},
				language: traduccion_datatable,
				dom: 'Bfrtip',
				buttons: [
					{"extend": 'pdf', "text":'Export',"className": 'btn btn-danger', "orientation": 'landscape', title: 'Roles'},
					{"extend": 'copy', "text":'Export',"className": 'btn btn-primary', title: 'Roles'},
					{"extend": 'excel', "text":'Export',"className": 'btn btn-success', title: 'Roles'},
					{"extend": 'print', "text":'Export',"className": 'btn btn-secondary', title: 'Roles'}
				],
				initComplete: function () {
					$('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
					$('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
					$('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
					$('.buttons-print').html('<span class="glyphicon glyphicon-print" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir');
				}
			});

			$select_categorias = $('#categoria');
			$.ajax({
				url: "{{ url('categoria/ajax_dropdown') }}"
				, "type": "POST"
				, data:{length:'',start:0}
				, dataType: 'JSON'
				, success: function (data) {
					//clear the current content of the select
					$select_categorias.html('');
					//iterate over the data and append a select option
					$.each(data.categorias, function (key, val) {
						$select_categorias.append('<option value="' + val.id + '">' + val.nombre + '</option>');
					})
				}
				, error: function () {
					$select_categorias.html('<option id="-1">Ninguna disponible</option>');
				}
			});*/
		});
/*
		function reload_table()
		{
			table.ajax.reload(null,false);
		}

		function add_rol()
		{
			save_method = 'add';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#modal_form').modal('show');
			$('.modal-title').text('Agregar roles');
			$('#accion').val('add');
		}

		function edit_rol(id)
		{
			save_method = 'update';
			$('#form')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#accion').val('edit');


			$.ajax({
				url : "{{ url('roles/ajax_edit/') }}" + "/" + id,
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

					$('#modal_form').modal('show');
					$('.modal-title').text('Editar rol');
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					show_ajax_error_message(jqXHR, textStatus, errorThrown);
				}
			});
		}

		function delete_rol(id)
		{
			if(confirm('¿Desea borrar el rol?'))
			{

				$.ajax({
					url : "{{ url('roles/ajax_delete') }}"+"/"+id,
					type: "POST",
					dataType: "JSON",
					success: function(data)
					{
						swal("Aviso", "Rol eliminado con éxito.", "success");

						$('#modal_form').modal('hide');
						reload_table();
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});

			}
		}*/
	</script>

	<!--LISTADO-->
	<div class="container">
		
		<div class="row">
			<div class="col-md-12">
				<h2>Roles</h2>
				<a data-toggle="collapse" data-target="#opciones-roles"><div class="colapsable-aleph"><span class="glyphicon glyphicon-chevron-right"></span> Opciones</div></a>
				<div id="opciones-roles" class="collapse">
					<form action="{{ url('roles/opciones_submit') }}" method="post">
						<div class="row">
							<div class="form-body">
								<div class="form-group">
									<label class="control-label col-md-3">Utilizar ID de grupo para asociar al rol en integración de login</label>
									<div class="col-md-9">
										<input type="checkbox" value="1"  name="utilizar_id_grupo" id="utilizar_id_grupo">
										<span class="help-block"></span>
									</div>
								</div>
							</div>
						</div>
						<br>
						<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar opciones</button>
					</form>
					<hr>
				</div>
				<br>
				<button id="agregar" class="btn btn-success" onclick="add_rol()"><i class="glyphicon glyphicon-plus"></i> Agregar rol</button>
				<br>
				<br>
				<table id="roles-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<th>Nombre</th>
						<th style="width:125px;">Acción</th>
					</thead>
					<tbody>
						@foreach ($roles as $rol)
							<tr>
								<td>{{ $rol->nombre }}</td>
								<td>
									<form action="{{ route('roles.destroy', $rol->id) }}" method="POST">
										<a class="btn btn-sm btn-success" href="{{ route('roles.edit', $rol->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}</a>
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-danger btn-sm" 
											onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;">
											<i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
									</form>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				
			</div>
		</div>

	</div>


</x-app-layout>
