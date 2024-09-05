<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Usuarios') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard-bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 45rem;">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="max-w-xl">
					Opciones
				</div>
			</div>

			<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="max-w-xl">
					Filtros
				</div>
			</div>

			<div class="p-12 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="max-w-xl">
					<div class="table-responsive">
						<table id="example" class="table stripe hover cell-border">
							<thead class="thead">
								<tr>
									<th>Nombre</th>
									<th>Apellido</th>
									<th>Username</th>
									<th>Email</th>
									<th>Rol</th>
									<th>Habilitado</th>
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
									<td>{{ $user->rol }}</td>
									<td>{{ $user->habilitado }}</td>
									<!--td>
										<a class="btn btn-sm btn-warning " href="{{ route('users.show', $user->id) }}"><i class="fa-solid fa-eye"></i> Mostrar</a>
									</td-->
									<td>
										<a class="btn btn-sm btn-warning" href="{{ route('users.edit', $user->id) }}"><i class="fa-solid fa-pen-to-square"></i>Editar</a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>


</x-app-layout>