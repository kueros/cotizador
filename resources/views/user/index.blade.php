<x-app-layout title="Usuarios" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Usuarios') }}
		</h2>
	</x-slot>
	<script type="text/javascript">
		

		jQuery(document).ready(function($){
			$('#usuarios-table').DataTable({
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

	</script>

	<div class="container-full-width">
		<div class="row">
			<div class="col-md-12">
				<h2>Usuarios</h2>
				<a data-toggle="collapse" data-target="#opciones-usuario"><div class="colapsable-aleph"><span class="glyphicon glyphicon-chevron-right"></span> Opciones</div></a>
				<div id="opciones-usuario" class="collapse">
					<form action="{{ url('usuarios/opciones_submit') }}" method="post">
						<div class="row">
							<div class="form-body">
								<div class="form-group">
									<label class="control-label col-md-3">Tiempo de sesión (segundos)</label>
									<div class="col-md-3">
										<input class="form-control" name="session_time" type="number" min="60" max="864000" step="1" value=""/>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-body">
								<div class="form-group">
									<label class="control-label col-md-3">Habilitar múltiples roles</label>
									<div class="col-md-3">
										<input type="checkbox" value="1" name="usuarios_habilita_multiples_roles" id="usuarios_habilita_multiples_roles">
									</div>
								</div>
							</div>
						</div>

						
						<br>
						<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar opciones</button>
					</form>
					<hr>
				</div>
				<button id="agregar" class="btn btn-success" onclick="add_usuario()"><i class="glyphicon glyphicon-plus"></i> Agregar usuario</button>
				<button id="importar" class="btn btn-primary" onclick="importar_usuarios()"><i class="glyphicon glyphicon-import"></i> Importar usuarios</button>
				<a class="btn btn-info" href="{{ url('usuarios/campos_adicionales') }}"><i class="glyphicon glyphicon-th-list"></i> Campos adicionales</a>
				<br>
				<br>
				<div class="form-group">
					<label class="control-label">Filtro de usuarios</label>
					<select class="form-control" name="filtrar_usuarios" id="filtrar_usuarios" onchange="filtrar_usuarios()" style="width:200px;">
						<option value="0">Solo habilitados</option>
						<option value="1">Todos</option>
						<option value="2">Eliminados</option>
					</select>
				</div>
				<br>
				
				<table id="usuarios-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Apellido</th>
							<th>Username</th>
							<th>Email</th>
							<th>Rol</th>
							<th>Habilitado</th>
							<th>Bloqueado</th>
							<th colspan="2" class="text-center">Acciones</th>
							<!--<th style="width:125px;">Acción</th>-->
						</tr>
					</thead>
					<tbody>
						@foreach ($users as $user)
							<tr>
								<td>{{ $user->nombre }}</td>
								<td>{{ $user->apellido }}</td>
								<td>{{ $user->username }}</td>
								<td>{{ $user->email }}</td>
								<td>{{ $user->nombre_rol }}</td>
								<td>{{ $user->habilitado ? 'Sí' : 'No' }}</td>
								<td>{{ $user->bloqueado ? 'Sí' : 'No' }}</td>
								<td>
									<form action="{{ route('users.destroy', $user->id) }}" method="POST">
										<a class="btn btn-sm btn-success" href="{{ route('users.edit', $user->id) }}">
											<i class="fa fa-fw fa-edit" title="Editar" ></i>
										</a>
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('¿Estás seguro de eliminar este usuario?') ? this.closest('form').submit() : false;">
											<i class="fa fa-fw fa-trash" title="Borrar" ></i>
										</button>

										<!-- Botón para abrir el formulario de cambiar contraseña >
										<a href="{{ route('users.showPasswordForm', $user->id) }}" class="btn btn-warning btn-sm">
											{{ __('Cambiar contraseña') }}
										</a-->
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
