<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Usuarios') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard_bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<!-- Acordeón para Opciones -->
			<div x-data="{ open: false }" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
				<button @click="open = !open" class="flex justify-between items-center w-full p-4 font-medium text-left text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-gray-500">
					Opciones
					<svg :class="{ 'rotate-180': open, 'rotate-0': !open }" class="w-6 h-6 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M5.293 9.707a1 1 0 011.414 0L10 13.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
					</svg>
				</button>
				<div x-show="open" class="mt-2">
					<!-- Contenido de Opciones -->
					<form action="https://demo.alephmanager.com/usuarios/opciones_submit" method="post">
						<div class="row">
							<div class="form-body">
								<div class="form-group">
									<label class="control-label col-md-3">Requerir cambio de contraseña después de 30 días</label>
									<div class="col-md-9">
										<input type="checkbox" value="1" name="request_change" id="request_change">
										<span class="help-block"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-body">
								<div class="form-group">
									<label class="control-label col-md-3">Configurar contraseñas</label>
									<div class="col-md-3">
										<input type="checkbox" value="1" name="configurar_claves" id="configurar_claves">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-body">
								<div class="form-group">
									<label class="control-label col-md-3">Permitir múltiples sesiones</label>
									<div class="col-md-3">
										<input type="checkbox" value="1" checked="" name="permitir_multiples_sesiones" id="permitir_multiples_sesiones">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-body">
								<div class="form-group">
									<label class="control-label col-md-3">Tiempo de sesión (segundos)</label>
									<div class="col-md-3">
										<input class="form-control" name="session_time" type="number" min="60" max="864000" step="1" value="900">
									</div>
								</div>
							</div>
						</div>
						<br>
						<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar opciones</button>
						<input type="hidden" name="form_token" value="86eefd547e41146d8e40548fd734bc96">
					</form>
				</div>
			</div>

			<!-- Acordeón para Filtros -->
			<div x-data="{ open: false }" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
				<button @click="open = !open" class="flex justify-between items-center w-full p-4 font-medium text-left text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-gray-500">
					Filtros
					<svg :class="{ 'rotate-180': open, 'rotate-0': !open }" class="w-6 h-6 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M5.293 9.707a1 1 0 011.414 0L10 13.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
					</svg>
				</button>
				<div x-show="open" class="mt-2">
					<!-- Contenido de Filtros -->
					<div class="form-group">
						<label class="control-label">Filtro de usuarios</label>
						<select class="form-control" name="filtrar_usuarios" id="filtrar_usuarios" onchange="filtrar_usuarios()" style="width:200px;">
							<option value="0" selected="">Solo habilitados</option>
							<option value="1">Todos</option>
							<option value="2">Eliminados</option>
						</select>
					</div>
				</div>
			</div>

			<div class="p-12 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="table-responsive">
					<div class="float-right">
						<a href="{{ route('users.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
							{{ __('Nuevo Usuario') }}
						</a>
					</div>
					<table id="example" class="cell-border" style="width:100%">
						<thead class="thead">
							<tr>
								<th>Nombre</th>
								<th>Apellido</th>
								<th>Username</th>
								<th>Email</th>
								<th>Rol</th>
								<th>Habilitado</th>
								<th>Bloqueado</th>
								<th colspan="2" class="text-center">Acciones</th>
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
										<a class="btn btn-sm btn-success" href="{{ route('users.edit', $user->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
									</form>
								</td>



							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


</x-app-layout>
