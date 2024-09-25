<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Roles') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard_bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="max-w-xl">
					Opciones
				</div>
			</div>

			<div class="p-12 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="table-responsive">
					<div class="float-right">
						<a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
							{{ __('Nuevo Rol') }}
						</a>
					</div>
					<table id="example" class="cell-border" style="width:100%">
						<thead class="thead">
							<tr>
								<th>Nombre</th>
								<th colspan="2" class="text-center">Acciones</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($roles as $rol)
							<tr>
								<td>{{ $rol->nombre }}</td>
								<td>

								@php
								$rol_id = $roles->firstWhere('nombre', 'Administrador')->id;
								@endphp
								@if ($rol->id != $rol_id)
								<form action="{{ route('roles.destroy', $rol->id) }}" method="POST">
										<a class="btn btn-sm btn-success" href="{{ route('roles.edit', $rol->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}</a>
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
									</form>
								@endif
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
