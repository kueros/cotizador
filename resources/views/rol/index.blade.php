<x-app-layout>
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
						@if ($permiso_agregar_roles)
						<a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
							{{ __('Nuevo Rol') }}
						</a>
						@endif
					</div>
					<table id="example" class="table table-striped">
						<thead class="thead">
							<tr>
								<th style="width:75%; " >Nombre</th>
								<th >Acciones</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($roles as $rol)
							<tr>
								<td>{{ $rol->nombre }}</td>
								<td>
								@php
								$rol_id = $roles->firstWhere('nombre', 'Administrador')->rol_id;
								@endphp
                                @if ($rol->rol_id != $rol_id)
									@if ($permiso_editar_roles)
									<a class="btn btn-sm btn-success" style="float:left;" href="{{ route('roles.edit', $rol->rol_id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}
									</a>
									@endif
									@if ($permiso_eliminar_roles)
									<form action="{{ route('roles.destroy', $rol->rol_id) }}" method="POST">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Está seguro de querer borrar?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
										</form>
									@endif
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
