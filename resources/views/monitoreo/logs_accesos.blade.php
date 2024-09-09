<x-app-layout>
	<?php #dd($logs_accesos); 
	?>

	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Logs de Accesos') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard-bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
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
							@foreach ($logs_accesos as $log)
							<?php #dd($log); ?>
							<tr>
								<td>{{ $log->id }}</td>
								<td>{{ $log->email }}</td>
								<td>{{ $log->ip_address }}</td>
								<td>{{ $log->user_agent }}</td>
								<td>{{ $log->created_at }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


</x-app-layout>