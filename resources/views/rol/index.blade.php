<x-app-layout title="Roles" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Roles') }}
		</h2>
	</x-slot>
	<!-- Primary Navigation Menu -->
	@php
	$user = Auth::user()->username;
	$email = Auth::user()->email;
	$permiso_agregar_roles = tiene_permiso('add_rol');
	$permiso_editar_roles = tiene_permiso('edit_rol');
	$permiso_eliminar_roles = tiene_permiso('del_rol');
	@endphp

	<script type="text/javascript">
		var table;
		var save_method;

		jQuery(document).ready(function($){
			table = $('#roles-table').DataTable({
				"ajax": {
					url : "{{ url('roles/ajax_listado') }}",
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
					{"extend": 'pdf', "text":'Export',"className": 'btn btn-danger', "orientation": 'landscape', title: 'Roles'},
					{"extend": 'copy', "text":'Export',"className": 'btn btn-primary', title: 'Roles'},
					{"extend": 'excel', "text":'Export',"className": 'btn btn-success', title: 'Roles'},
					{"extend": 'print', "text":'Export',"className": 'btn btn-secondary', title: 'Roles'}
				],
				initComplete: function () {
					$('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
					$('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
					$('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
					$('.buttons-print').html('<span class="bi bi-printer" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir');
				}
			});
		});

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
			$('#form').attr('action', "{{ url('roles') }}");
    		$('#method').val('POST');
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
					$('#form').attr('action', "{{ url('roles') }}" + "/" + id);
            		$('#method').val('PUT');
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
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function(data)
					{
						swal.fire("Aviso", "Rol eliminado con éxito.", "success");

						$('#modal_form').modal('hide');
						reload_table();
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
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
				<h2>Roles</h2>
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
					<div class="float-right">
						<button id="agregar" class="btn btn-success" onclick="add_rol()">
							<i class="bi bi-plus"></i> {{ __('Nuevo Rol') }}
						</button>
					</div>
					<br>
					<table id="roles-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
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
					<h5 class="modal-title">Formulario roles</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" action="">
                	@csrf
					<input name="_method" type="hidden" id="method">
					<div class="modal-body form">					
						<input type="hidden" value="" name="id"/>
						<input name="accion" id="accion" class="form-control" type="hidden">
						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">Nombre</label>
								<div class="col-md-9">
									<input name="nombre" maxlength="255" placeholder="Nombre del rol" class="form-control" type="text">
									<span class="help-block"></span>
								</div>
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
