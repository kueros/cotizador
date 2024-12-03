<x-app-layout title="Log acciones" :breadcrumbs="[['title' => 'Inicio', 'url' => route('dashboard')], ['title' => 'Monitoreo', 'url' => route('monitoreo.index')]]">
	
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Log de Acciones') }}
		</h2>
	</x-slot>

	<style>
		/* #log-acciones-table{
			table-layout:fixed;
		} */
		
		#log-acciones-table tbody td{
			word-wrap:break-word!important;
		}
		table{
		margin: 0 auto;
		width: 100%;
		clear: both;
		border-collapse: collapse;
		table-layout: fixed;
		word-wrap:break-word; 
		}
	</style>
	<script type="text/javascript">
		var table;
		var save_method;

		jQuery(document).ready(function($){
			table = $('#log-acciones-table').DataTable({
				"ajax": {
					url : "{{ url('monitoreo/ajax_log_administracion') }}",
					type : 'GET'
				},
				"order": [[ 0, "desc" ]],
				"ordering": true,
				bAutoWidth: false, 
				aoColumns : [
					{ sWidth: '5%' },
					{ sWidth: '10%' },
					{ sWidth: '30%' },
					{ sWidth: '15%' },
					{ sWidth: '15%' }
				],
				language: traduccion_datatable,
				"pageLength": 100,
				//dom: 'Bfrtip',
				layout: {
					topStart: {
						buttons: [
							{"extend": 'copy', "text":'Portapapeles',"className": 'btn btn-primary', title: 'Log de acciones'},
							{"extend": 'pdf', "text":'PDF',"className": 'btn btn-danger', title: 'Log de acciones'},
							{"extend": 'excel', "text":'Excel',"className": 'btn btn-success', title: 'Log de acciones'},
							{"extend": 'print', "text":'Imprimir',"className": 'btn btn-secondary', title: 'Log de acciones'}
						],
					},
					bottomEnd: {
						paging: {
							firstLast: false  // Esto debería eliminar los botones "Primero" y "Último"
						}
					}
				},
				columnDefs: [
					{ targets: '_all', className: 'text-start' },
				],
				initComplete: function () {
					$('.buttons-copy').html('<i class="fas fa-copy"></i> Portapapeles');
					$('.buttons-pdf').html('<i class="fas fa-file-pdf"></i> PDF');
					$('.buttons-excel').html('<i class="fas fa-file-excel"></i> Excel');
					$('.buttons-print').html('<span class="bi bi-printer" data-toggle="tooltip" title="Exportar a PDF"/> Imprimir');
				}
			});
		});
	</script>

	<div class="container-full-width">
		<div class="row table-responsive">
			<div class="col-md-12">
				<h2>Log de acciones</h2>
				<table id="log-acciones-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<th>ID</th>
						<th>Usuario</th>
						<th>Detalle</th>
						<th>Fecha</th>
						<th>IP</th>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>


</x-app-layout>
