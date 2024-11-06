<x-app-layout title="Monitoreo" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Usuarios') }}
		</h2>
	</x-slot>

	<style>
		.div-contenedor-acceso {
			height: 200px;
			width: 200px;
			padding: 50px;
			border: solid 2px black;
			text-align: center;
			background: #556da326;
			border-radius: 8px;
			margin-bottom: 20px;
		}

		.div-contenedor-acceso:hover {
			box-shadow: 0px 0px 30px rgba(73, 78, 92, 0.8);
		}

		.div-logs-img {
			height: 100%;
			width: 100%;
		}

		#log_accesos {
			padding: 5px;
			background-image: url('images/log_accesos.png');
			background-size: contain;
			background-repeat: no-repeat;
			background-position: center;
		}

		#log_administracion {
			padding: 5px;
			background-image: url('images/log_acciones.png');
			background-size: contain;
			background-repeat: no-repeat;
			background-position: center;
		}

		#log_notificaciones {
			padding: 5px;
			background-image: url('images/log_notificaciones.png');
			background-size: contain;
			background-repeat: no-repeat;
			background-position: center;
		}

		#log_emails {
			padding: 5px;
			background-image: url('images/log_emails.png');
			background-size: contain;
			background-repeat: no-repeat;
			background-position: center;
		}

		.monitoreo-title {
			text-decoration: none !important;
			color: black;
			font-weight: bold;
		}
	</style>

	<div class="container">
		<div class="row mt-4">
			<h2>Monitoreo</h2>
		</div>
		<div class="row mt-5">
			<!-- Tarjeta 1: Log de accesos -->
			<div class="col-md-3 col-sm-6">
				<a class="link_monitoreo" href="<?= route('monitoreo.log_accesos') ?>">
					<div class="div-contenedor-acceso">
						<div class="div-logs-img" id="log_accesos"></div>
						<span class="monitoreo-title">Log de accesos</span>
					</div>
				</a>
			</div>

			<!-- Tarjeta 2: Log de administración -->
			<div class="col-md-3 col-sm-6">
				<a class="link_monitoreo" href="<?= route('monitoreo.log_administracion') ?>">
					<div class="div-contenedor-acceso">
						<div class="div-logs-img" id="log_administracion"></div>
						<span class="monitoreo-title">Log de acciones</span>
					</div>
				</a>
			</div>

			<!-- Tarjeta 3: Log de notificaciones -->
			<div class="col-md-3 col-sm-6">
				<!-- <a class="link_monitoreo" href="<?= route('monitoreo.log_notificaciones') ?>"> -->
					<div class="div-contenedor-acceso">
						<div class="div-logs-img" id="log_notificaciones"></div>
						<span class="monitoreo-title">Log de notificaciones</span>
					</div>
				<!-- </a> -->
			</div>

			<!-- Tarjeta 4: Log de emails -->
			<div class="col-md-3 col-sm-6">
				<!-- <a class="link_monitoreo" href="<?= route('monitoreo.log_emails') ?>"> -->
					<div class="div-contenedor-acceso">
						<div class="div-logs-img" id="log_emails"></div>
						<span class="monitoreo-title">Log de emails</span>
					</div>
				<!-- </a> -->
			</div>
		</div>
	</div>
</x-app-layout>