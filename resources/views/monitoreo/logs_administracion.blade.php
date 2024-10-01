<x-app-layout>
	<?php #dd($logs_administracion); 
	?>

	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Logs de Administracion') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard_bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="table-responsive">
					<table id="example" class="cell-border" style="width:100%">
						<thead class="thead">
							<tr>
								<th>ID</th>
								<th>Usuario</th>
								<th>Detalle</th>
								<th>Fecha</th>
								<th>IP</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($logs_administracion as $log)
							<?php #dd(Auth::user()); ?>
							<tr>
								<td>{{ $log->id }}</td>
								<td>{{ $log->username }}</td>
								<td>{{ $log->detalle }}</td>
								<td>{{ $log->ip }}</td>
								<td nowrap>{{ $log->created_at }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


</x-app-layout>
