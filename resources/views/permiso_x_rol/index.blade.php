<?php
use App\Models\Permiso_x_Rol;

?>

<x-app-layout title="Asignación de Permisos" :roles="$roles" :breadcrumbs="[['title' => 'Inicio', 'url' => route('dashboard')]]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Asignación de permisos') }}
		</h2>
	</x-slot>

	<div class="container-full-width" id="pagina-permisos">
		<div class="row">
			<div class="col-md-12">
				<h2>Asignación de permisos</h2>
				<br>
				@include('layouts.partials.message')
				<!-- Formulario para guardar permisos -->
				<form action="{{ route('permisos_x_rol.update') }}" method="POST">
					@csrf
					
					<div class="accordion" id="accordionPermisosXRol">
						@foreach ($secciones as $seccion)
							<div class="accordion-item">
								<h2 class="accordion-header" id="headingOne">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
										data-bs-target="#permiso_{{ $seccion->seccion_id }}" aria-expanded="true" aria-controls="collapseOne">
										{{ $seccion->nombre }}
									</button>
								</h2>
								<div id="permiso_{{ $seccion->seccion_id }}" class="accordion-collapse collapse" aria-labelledby="headingOne" 
									data-bs-parent="#accordionPermisosXRol">
									<div class="accordion-body">
										<div class="card">
											<div class="card-body">
												<table id="modulo_{{ $seccion->seccion_id }}" class="table table-striped">
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
																<td>{{ $permiso->descripcion }}</td>
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
														@endforeach
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						@endforeach
					</div>

					<!-- Botón para guardar cambios -->
					<button type="submit" class="btn btn-primary mt-4">Guardar cambios</button>
				</form>
			</div>
		</div>
	</div>
</x-app-layout>