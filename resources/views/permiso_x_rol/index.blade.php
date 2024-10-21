<?php
use App\Models\Permiso_x_Rol;

?>

<x-app-layout :roles="$roles">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Permisos x Rol') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard_bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="p-12 sm:p-8 bg-white shadow sm:rounded-lg">
				<div>
					<!-- Formulario para guardar permisos -->
					<form action="{{ route('permisos_x_rol.update') }}" method="POST">
						@csrf
						
						@foreach ($secciones as $seccion)
							<!-- Acordeón por sección -->
							<div x-data="{ open2: false }" class="border-t border-gray-200 mb-6">
								<button @click.prevent="open2 = !open2" class="flex justify-between items-center w-full p-4 font-medium text-left text-gray-800 bg-gray-100 hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-gray-500">
									{{ $seccion->nombre }}
									<svg :class="{ 'rotate-180': open2, 'rotate-0': !open2 }" class="w-6 h-6 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
										<path fill-rule="evenodd" d="M5.293 9.707a1 1 0 011.414 0L10 13.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
									</svg>
								</button>
								
								<!-- Contenido de cada sección -->
								<div x-show="open2" x-cloak class="mt-2 p-4 border-t border-gray-200">
									<div class="card">
										<div class="card-body">
											<table id="modulo_{{ $seccion->id }}" class="table table-striped">
												<thead>
													<tr>
														<th>Permisos</th>
														<?php #dd($roles); ?>
														@foreach ($roles as $rol)
															<th>{{ $rol->nombre }}</th>
														@endforeach
													</tr>
												</thead>
														<?php 
														#dd($permisosRoles); 
														#dd($permisos);
														#dd($secciones);
														#dd($roles);
														?>
												<tbody>
@foreach ($permisos as $permiso)
<tr>
    @if ($permiso->seccion_id == $seccion->seccion_id)
        <td>{{ $permiso->nombre }}</td>
        @foreach ($roles as $rol)
        <td>
            <?php
            $checked = "";
            $permisoRol = Permiso_x_Rol::where('rol_id', $rol->rol_id)
                                        ->where('permiso_id', $permiso->id)
                                        ->first();
            ?>
            <!-- Comprobación de si $permisoRol existe -->
            @if ($permisoRol)
                @if ($permisoRol->habilitado == 1)
                    <?php $checked = "checked"; ?>
                @endif
                <input type="checkbox" name="id[{{$permisoRol->id}}]" value="1" {{ $checked }}>
            @else
                <!-- Si no existe el registro, checkbox desmarcado -->
                <input type="checkbox" name="id[new_{{$rol->rol_id}}_{{$permiso->id}}]" value="1">
            @endif
        </td>
        @endforeach
    @endif
</tr>
@endforeach												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						@endforeach

						<!-- Botón para guardar cambios -->
						<button type="submit" class="btn btn-primary mt-4">Guardar cambios</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>