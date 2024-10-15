<x-app-layout :rols="$rols">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Permisos x Rol') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard_bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="p-12 sm:p-8 bg-white shadow sm:rounded-lg">
                <div >
					<!-- Acordeón para Opciones avanzadas -->
					<div x-data="{ open2: false }" class="border-t border-gray-200">
						<button @click="open2 = !open2" class="flex justify-between items-center w-full p-4 font-medium text-left text-gray-800 bg-gray-100 hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-gray-500">
							Opciones avanzadas
							<svg :class="{ 'rotate-180': open2, 'rotate-0': !open2 }" class="w-6 h-6 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M5.293 9.707a1 1 0 011.414 0L10 13.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
							</svg>
						</button>
						<div x-show="open2" class="mt-2 p-4 border-t border-gray-200">
							<!-- Contenido de Opciones avanzadas -->
							<form action="{{ route('permisos_x_rol.update') }}" method="POST">
								@csrf
								@foreach ($modulos as $modulo)
									<div class="card mb-4">
										<div class="card-header">
											<h3>{{ $modulo->nombre }}</h3>
										</div>
										<div class="card-body">
											<table id="modulo_{{ $modulo->id }}" class="table table-striped">
												<thead>
													<tr>
														<th>Permisos</th>
														@foreach ($rols as $rol)
															<th>{{ $rol->nombre }}</th>
														@endforeach
													</tr>
												</thead>
												<tbody>
													@foreach ($modulo->permisos as $permiso)
														<tr>
															<td>{{ $permiso->nombre }}</td>
															@foreach ($rols as $rol)
																<td>
																	<input type="checkbox" name="permisos[{{ $rol->rol_id }}][{{ $permiso->id }}]" value="1"
																		@if($rol->permisos && $rol->permisos->pluck('id')->contains($permiso->id)) checked @endif>
																</td>
															@endforeach
														</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								@endforeach
								<button type="submit" class="btn btn-primary mt-4">Guardar cambios</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>