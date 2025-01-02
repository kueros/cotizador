<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Permisos x Rol') }}
		</h2>
	</x-slot>

	<div
		style="background-image: url('/build/assets/images/dashboard_bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="max-w-xl">
					Opciones
				</div>
			</div>

			<div class="p-12 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="table-responsive">
					<form action="{{ route('permisos_x_rol.update') }}" method="POST">
						@csrf
						<table id="example" class="cell-border" style="width:100%">
							<thead class="thead">
								<tr>
									<th></th>
									<th>Permisos</th>
									@foreach ($rols as $rol)
                                        <th>{{ $rol->nombre }}</th>
                                    @endforeach
								</tr>
							</thead>
							<tbody id="sortable-permisos">
								@foreach ($permisos as $permiso)
                                    <tr data-id="{{ $permiso->id }}">
                                        <td><i class="fas fa-bars"></i></td>
                                        <td>{{ $permiso->nombre }}</td>
                                        @foreach ($rols as $rol)
                                            <td>
                                                <input type="checkbox" name="permisos[{{ $rol->id }}][{{ $permiso->id }}]"
                                                    value="1" @if($rol->permisos && $rol->permisos->pluck('id')->contains($permiso->id)) checked @endif>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
							</tbody>
						</table>
						<button type="submit" class="btn btn-primary mt-4">Guardar cambios</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			new Sortable(document.getElementById('sortable-permisos'), {
				animation: 150,
				ghostClass: 'blue-background-class',
				handle: 'i.fa-bars', // Usa el icono como el handle para mover las filas
				onEnd: function (evt) {
					let order = [];
					document.querySelectorAll('#sortable-permisos tr').forEach(function (tr, index) {
						order.push({
							id: tr.getAttribute('data-id'),
							orden: index + 1 // Orden empieza en 1
						});
					});

					// Enviar el nuevo orden al servidor con AJAX
					fetch('{{ route("permisos.updateOrder") }}', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						},
						body: JSON.stringify({ orden: order })
					})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								console.log('Orden actualizado correctamente');
							} else {
								console.error('Error al actualizar el orden');
							}
						})
						.catch(error => console.error('Error:', error));
				}
			});
		});
	</script>
</x-app-layout>
