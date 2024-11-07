<x-app-layout title="Configuración" :breadcrumbs="[['title' => 'Inicio', 'url' => route('dashboard')]]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ __('Configuraciones') }}
		</h2>
	</x-slot>

	<script>
		const loadingModal = document.getElementById('loadingModal');
		const successMessage = document.getElementById('successMessage');

		// Función para mostrar el modal
		function showLoading() {
			loadingModal.style.display = 'block';
		}

		// Función para ocultar el modal
		function hideLoading() {
			loadingModal.style.display = 'none';
		}

		// Función para mostrar el mensaje de éxito
		function showSuccessMessage() {
			successMessage.style.display = 'block';

			// Ocultar el mensaje después de 3 segundos (opcional)
			setTimeout(() => {
				successMessage.style.display = 'none';
			}, 3000);
		}

		// Manejamos el evento de clic de los checkboxes
		jQuery(document).ready(function($){
			document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
				checkbox.addEventListener('change', function() {
					showLoading();

					// Aquí haces la solicitud de actualización (AJAX, Fetch API, etc.)
					fetch('/configuracion/guardar_estado', {
						method: 'POST',
						body: JSON.stringify({ id: this.id, checked: this.checked }),
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						}
					})
					.then(response => response.json())
					.then(data => {
						// Ocultamos el modal justo antes de mostrar el mensaje de éxito
						hideLoading();

						// Mostramos el mensaje de éxito
						showSuccessMessage();
					})
					.catch(error => {
						console.error('Error al guardar:', error);

						// Ocultamos el modal aunque haya un error
						hideLoading();
					});
				});
			});

			$('input[type="checkbox"]').on('change', function() {
				// Guardo el nombre del checkbox que ha sido clickeado
				let variableName = $(this).attr('name');
				// Verifico si está checkeado
				let isChecked = $(this).is(':checked');
				// Obtengo el div correspondiente basado en el id del checkbox
				let targetDiv = $('#div_' + variableName);
				// Si está checkeado, muestro el formulario
				if (isChecked) {
					targetDiv.slideUp(); // Oculto el formulario
				} else {
					targetDiv.slideDown(); // Muestro el formulario
				}
				// Envío el AJAX para guardar el estado
				$.ajax({
					url: '{{ route("configuracion.guardar_estado") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						[variableName]: isChecked ? 1 : 0
					},
					dataType: "JSON",
					success: function(response) {
						alert('Estado guardado correctamente');
						if (variableName == 'notificaciones_email_aleph') {
							window.location.href = '{{ route("configuracion.index") }}';
						} else {
							location.reload(); // Refresca la pantalla
						}
					},
					error: function(error) {
						alert('Error al guardar el estado', error);
					}
				});
			});


			$('#btn_guardar_remitente').on('click', function(event) {
				// Evitar el envío del formulario
				event.preventDefault();

				// Recopilar los datos del formulario
				let from1 = $('input[name="from"]').val();
				let from_name1 = $('input[name="from_name"]').val();
				console.log(from1, from_name1);
				// Enviar el AJAX
				$.ajax({
					url: '{{ route("configuracion.guardar_remitente") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						from: from1,
						from_name: from_name1
					},
					dataType: "JSON",
					success: function(response) {
						swal.fire("Aviso", "El remitente ha sido guardado correctamente.", "success").then(function(){
							location.reload();
						});
					},
					error: function(error) {
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
						console.log('Error al guardar el remitente');
					}
				});
			});

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
		});

		function add_parametro(){
			$('#form_parametro')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#modal_form_parametro').modal('show');
			$('.modal-title_parametro').text('Agregar parametro');
			$('#accion_parametro').val('add');
		}

		function probar_configuracion_email(){
			show_loading();
			$.ajax({
				url : "",
				type: "POST",
				dataType: "JSON",
				success: function(data){
						hide_loading();
						swal.fire("Aviso", "La configuración es correcta OK.", "success");
					},
				error: function(jqXHR, textStatus, errorThrown) {
						show_ajax_error_message(jqXHR, textStatus, errorThrown);
					}
				});
		}

		function delete_parametro_email(parametro){
			swal.fire({
				title: "¿Está seguro que desea borrar el parametro?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: "Sí, borrar",
				cancelButtonText: "Cancelar"
			}).then((result) => {
				if (result.isConfirmed) {
					console.log($('meta[name="csrf-token"]').attr('content')); // Asegúrate de que no sea `undefined`
					console.log(parametro);
					$.ajax({
						url : "{{ route('configuracion.ajax_delete_parametro_email') }}",
						data: {parametro:parametro},
						type: "POST",
						dataType: "JSON",
						headers: {
							'X-CSRF-TOKEN': '{{ csrf_token() }}'
						},
						success: function(data){
							swal.fire("Aviso", "El parametro ha sido eliminado", "success").then(function(){
								location.reload();
							});
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							show_ajax_error_message(jqXHR, textStatus, errorThrown);
						}
					});
				}
			});
		}


	</script>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h2>Configuraciones</h2>


				@include('layouts.partials.message')
				<div class="accordion" id="accordionConfiguraciones">
					<div class="accordion-item">
						<h2 class="accordion-header" id="headingOne">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
								data-bs-target="#notificaciones" aria-expanded="true" aria-controls="collapseOne">
								Notificaciones
							</button>
						</h2>
						<div id="notificaciones" class="accordion-collapse collapse" aria-labelledby="headingOne" 
							data-bs-parent="#accordionConfiguraciones">
							<div class="accordion-body">
								<br>
								
								<!--<form id="form_notificaciones" action="{{-- route('configuracion.guardar_estado') --}}" method="POST">-->
									@csrf
									<?php
									#dd($variables->firstWhere('nombre', 'notificaciones_email')->valor);
									$notificaciones_email = $variables->firstWhere('nombre', 'notificaciones_email')->valor;
									$notificaciones_email_aleph = $variables->firstWhere('nombre', '_notificaciones_email_aleph')->valor;
									$notificaciones_email_from = $variables->firstWhere('nombre', '_notificaciones_email_from')->valor;
									$notificaciones_email_from_name = $variables->firstWhere('nombre', '_notificaciones_email_from_name')->valor;
									?>
									@foreach ($variables as $variable)
										@if(Str::startsWith($variable->nombre, 'noti'))
										<div class="row mb-3">
											<label class="col-md-6 col-form-label">{{ $variable->nombre_menu }}</label>
											<div class="col-md-6">
												<div class="form-check form-switch">
													<input class="form-check-input" name="{{ $variable->nombre }}" id="{{ $variable->nombre }}" 
														value="1" type="checkbox" {{ $variable->valor == 1 ? 'checked' : '' }} >
													<label class="form-check-label" for="{{ $variable->nombre }}"></label>
												</div>
											</div>
										</div>
										@endif
									@endforeach
								<!--</form>-->

								<!-- Contenido de Notificaciones -->
								<!-- resources/views/tu_vista.blade.php -->
								<div class="row mb-3">
									<label class="col-md-6 col-form-label">Utilizar servicio de envío de email de Aleph Manager</label>
									<div class="col-md-6">
										<div class="form-check form-switch">
											<input class="form-check-input" id="_notificaciones_email_aleph" name="_notificaciones_email_aleph" 
												value="1" type="checkbox" {{ $notificaciones_email_aleph == 1 ? 'checked' : '' }} >
											<label class="form-check-label" for="_notificaciones_email_aleph"></label>
										</div>
									</div>
									<?php
									if ($notificaciones_email == 1 && $notificaciones_email_aleph == 1) {
									?>
										<div class="col-md-12"><a href="{{ route('enviarmail') }}" class="btn btn-info">
												<span class="bi bi-check"></span> Probar envio de emails</a>
										</div>
									<?php
									}

									?>
								</div>
								<hr>
								<?php
								if ($notificaciones_email == 1 && $notificaciones_email_aleph == 0) {

								?>
									<h4>Configuración del remitente</h4><br>
									<!--<form id="form_config_remitente" action="{{ route('configuracion.guardar_remitente') }}" method="post">-->
										@csrf
										<div class="row mb-3">
											<label class="col-md-4 col-form-label">Email*</label>
											<div class="col-md-6">
												<input type="email" name="from" value="{{ $notificaciones_email_from }}" 
													class="form-control" required placeholder="info@alephmanager.com">
											</div>
										</div>
										<div class="row mb-3">
											<label class="col-md-4 col-form-label">Nombre*</label>
											<div class="col-md-6">
												<input type="email" name="from_name" value="{{ $notificaciones_email_from_name }}" 
													class="form-control" required placeholder="Aleph Manager">
											</div>
										</div>
										<button type="button" id="btn_guardar_remitente" class="btn btn-success">
											Guardar remitente
										</button>
									<!--</form>-->
									<hr>
									<br>

									<h4>Configuración de parametros email</h4>
									<div class="alert alert-warning" role="alert">
										Alterar la configuración puede implicar detener el envio de notificaciones via email, realice la prueba antes de guardar su configuración
									</div>
									<div class="alert alert-info" role="alert">
										Para conocer las configuraciones disponibles de la libreria siga <a class="alert-link" target="_blank" href="#">este enlace</a> y baje hasta la sección con el título "Email Preferences"
									</div>
									<button type="button" class="btn btn-success" onclick="add_parametro()">
										Agregar parametro
									</button>
									<br>
									<br>
									<table class="table table-striped table-bordered" cellspacing="0" width="100%">
										<thead>
											<th>Parametro</th>
											<th>Valor</th>
											<th>Acción</th>
										</thead>
										<tbody>
											@csrf
											<?php
											$nec = $variables->firstWhere('nombre', '_notificaciones_email_config')->valor;
											#dd($nec);
											$notificaciones_email_config = json_decode($nec);
											#var_dump($notificaciones_email_config);
											#die();
											if (!empty($notificaciones_email_config)) {
												foreach ($notificaciones_email_config as $parametro => $valor) {
													echo '<tr>';
													echo '<td>' . $parametro . '</td>';
													echo '<td>' . $valor . '</td>';
													echo '<td><a class="btn btn-danger" onclick="delete_parametro_email(\'' . $parametro . '\')"><i class="bi bi-trash"></i></a></td>';
													echo '</tr>';
												}
											} else {
												echo '<tr><td colspan="3">Sin parametros configurados</td></tr>';
											}
											?>
										</tbody>
									</table>
									<a class="btn btn-info" href="{{ route('enviarmail') }}">Probar configuración</a>
									<!--<a class="btn btn-info" onclick="enviarmail()">Probar configuración</a>-->
								<?php
								}
								#dd($notificaciones_email_aleph);
								?>

							</div>
						</div>
					</div>

					<div class="accordion-item">
						<h2 class="accordion-header" id="headingOne">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
								data-bs-target="#advance_options" aria-expanded="true" aria-controls="collapseOne">
								Opciones avanzadas
							</button>
						</h2>
						<div id="advance_options" class="accordion-collapse collapse" aria-labelledby="headingOne" 
							data-bs-parent="#accordionConfiguraciones">
							<div class="accordion-body">
								<br>
								<!-- Contenido de Opciones avanzadas -->
								<!--<form action="{{ route('configuracion.guardar_estado') }}" method="POST">-->
									@csrf
									<?php #dd($variables); ?>
									@foreach ($variables as $variable)
										@if(Str::startsWith($variable->nombre, 'opav'))
										<div class="row mb-3">
											<label class="col-md-6 col-form-label">{{ $variable->nombre_menu }}</label>
											<div class="col-md-6">
												<div class="form-check form-switch">
													<input class="form-check-input" name="{{ $variable->nombre }}" id="{{ $variable->nombre }}" 
														value="1" type="checkbox" {{ $variable->valor == 1 ? 'checked' : '' }} >
													<label class="form-check-label" for="{{ $variable->nombre }}"></label>
												</div>
											</div>
										</div>
										@endif
									@endforeach
								<!--</form>-->
							</div>
						</div>
					</div>
					<?php
 					$imagenHomePath = "";
					foreach ($variables as $variable) {
						if($variable->nombre == 'background_home_custom_path'){
							$imagenHomePath = $variable->valor;
						}
					}
					?>

				<div class="accordion-item">
					<h2 class="accordion-header" id="headingOne">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
							data-bs-target="#configuracion_pantallas" aria-expanded="true" aria-controls="collapseOne">
							Configuración de pantallas
						</button>
					</h2>
					<div id="configuracion_pantallas" class="accordion-collapse collapse" aria-labelledby="headingOne" 
						data-bs-parent="#accordionConfiguraciones">
						<div class="accordion-body">
							<br>
							<!-- Contenido de Configuración de pantallas -->
							@csrf
							@foreach ($variables as $variable)
								@if(Str::startsWith($variable->nombre, 'copa'))
								<div class="row mb-3">
									<label class="col-md-6 col-form-label">{{ $variable->nombre_menu }}</label>
									<div class="col-md-6">
										<div class="form-check form-switch">
											<input class="form-check-input" name="{{ $variable->nombre }}" id="{{ $variable->nombre }}" 
												value="1" type="checkbox" {{ $variable->valor == 1 ? 'checked' : '' }} 
												onclick="toggleImageUpload('{{ $variable->nombre }}')">
											<label class="form-check-label" for="{{ $variable->nombre }}"></label>
										</div>
									</div>
								</div>
								<div id="div_{{ $variable->nombre }}" style="display: {{ $variable->valor == 1 ? 'block' : 'none' }}">
									<form action="{{ route('configuracion.guardar_imagen') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
										@csrf
										<div class="form-group row">
											<label class="control-label col-md-4">Subir imagen</label>
											<div class="col-md-8">
												<input type="file" class="form-control" name="copa_path" accept="image/png, .jpeg, .jpg, .webp, image/gif" required>
												<button type="submit" class="btn btn-primary mt-2">Guardar</button>
											</div>
										</div>
									</form>                </div>
								@endif
							@endforeach
						</div>    
					</div>
				</div>

				<script>
				function toggleImageUpload(variableName) {
					console.log(variableName)
					const checkbox = document.getElementById(variableName);
					const uploadDiv = document.getElementById(`div_${variableName}`);
					uploadDiv.style.display = checkbox.checked ? 'block' : 'none';
				}
				</script>				
				</div>

			</div>
		</div>
	</div>

	<div class="modal fade" id="modal_form_parametro" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title_parametro">Formulario roles</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body form">
				<?php //route('configuracion/add_parametro_email') ?>
					<form action="{{ route('configuracion.add_parametro_email') }}" id="form_parametro" method="post" enctype="multipart/form-data" class="form-horizontal">
						@csrf
						<div class="form-body">
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">Parametro</label>
								<div class="col-md-9">
									<input name="parametro" required class="form-control" maxlength="255" type="text">
								</div>
							</div>
							<div class="mb-3 row">
								<label class="col-form-label col-md-3">Valor</label>
								<div class="col-md-9">
									<input name="valor" required class="form-control" maxlength="255" type="text">
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary">Guardar</button>
							<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>


	<div id="loadingModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background-color:rgba(0,0,0,0.5); color:white; padding:20px; border-radius:5px;">
		<p>Cargando...</p>
	</div>
	
</x-app-layout>