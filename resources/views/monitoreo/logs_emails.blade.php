<x-app-layout title="Log de Emails" :breadcrumbs="[['title' => 'Inicio', 'url' => route('dashboard')], ['title' => 'Monitoreo', 'url' => route('monitoreo.index')]]">
	
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Log de Emails') }}
		</h2>
	</x-slot>

	<style>
		/* #log-acciones-table{
			table-layout:fixed;
		} */
		
		#log-emails-table tbody td{
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
			table = $('#log-emails-table').DataTable({
				"ajax": {
					url : "{{ url('monitoreo/ajax_log_emails') }}",
					type : 'GET'
				},
				"order": [[ 3, "desc" ]],
				"ordering": false,
				bAutoWidth: false, 
				aoColumns : [
					{ sWidth: '5%' },
					{ sWidth: '30%' },
					{ sWidth: '15%' },
					{ sWidth: '20%' }, 	
					{ sWidth: '15%' }
				],
				language: traduccion_datatable,
				"pageLength": 100,
				dom: 'Bfrtip',
				buttons: [
					{"extend": 'pdf', "text":'Export',"className": 'btn btn-danger', title: 'Log de emails'},
					{"extend": 'copy', "text":'Export',"className": 'btn btn-primary', title: 'Log de emails'},
					{"extend": 'excel', "text":'Export',"className": 'btn btn-success', title: 'Log de emails'},
					{"extend": 'print', "text":'Export',"className": 'btn btn-secondary', title: 'Log de emails'}
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
				<h2>Log de emails</h2>
				<br>
				<br>
				<table id="log-emails-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<th>ID</th>
						<th>Fecha</th>
						<th>Email</th>
						<th>Detalle</th>
						<th>Enviado</th>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>


</x-app-layout>
