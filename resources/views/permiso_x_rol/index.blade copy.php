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
														#dd($permisoRol);
														#dd($secciones);
														#dd($roles);
														?>
												<tbody>
													<tr>
													@foreach ($permisosRoles as $permisoRol)
														@if ($permisoRol->seccion_id == $seccion->seccion_id) 
													<?php var_dump($permisoRol); ?>
															<!-- Filtra permisos por sección -->
																<td>{{ $permisoRol->permiso_nombre }}</td>
																<td>
																	@foreach ($roles as $rol)
																		<?php 
																		$checked = "";
																		if( $permisoRol->seccion_id == $seccion->seccion_id &&
																			$permisoRol->rol_id == $rol->rol_id &&
																			$permisoRol->habilitado == 1 )
																			{ $checked = "checked"; }
																		?>
																	<!-- Checkbox por cada rol y permiso -->
																	<input type="checkbox" name="permisos[{{ $rol->rol_id }}][{{ $permisoRol->permiso_id }}]" value="1"
																	<?php echo $checked; ?> >
																	@endforeach
																</td>
															@endif													
													@endforeach
													</tr>
												</tbody>
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