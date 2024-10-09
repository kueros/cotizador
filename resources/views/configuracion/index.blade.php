<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Configuraciones') }}
		</h2>
	</x-slot>
	<?php

	#dd($variables);

	?>
	<div style="background-image: url('/build/assets/images/dashboard_bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

			<!-- Contenedor de la "Card" -->
			<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

				<!-- Acordeón para Notificaciones -->
				<div x-data="{ open1: false }">
					<button @click="open1 = !open1" class="flex justify-between items-center w-full p-4 font-medium text-left text-gray-800 bg-gray-100 rounded-t-lg hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-gray-500">
						Notificaciones
						<svg :class="{ 'rotate-180': open1, 'rotate-0': !open1 }" class="w-6 h-6 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M5.293 9.707a1 1 0 011.414 0L10 13.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
						</svg>
					</button>
					<div x-show="open1" class="mt-2 p-4 border-t border-gray-200">

						<!-- Contenido de Notificaciones -->
						<!-- resources/views/tu_vista.blade.php -->
						<form id="form_notificaciones" action="{{ route('configuracion.guardar_estado') }}" method="POST">
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
								<div class="form-group row">
									<div class="col-md-6">
										<label>
											<input type="checkbox" id="{{ $variable->nombre }}" name="{{ $variable->nombre }}" value="1" {{ $variable->valor == 1 ? 'checked' : '' }} />
											{{ $variable->nombre_menu }}
										</label>
									</div>
								</div>
								@endif
							@endforeach
						</form>


						<!-- Contenido de Notificaciones -->
						<!-- resources/views/tu_vista.blade.php -->
						<div class="form-group row">
							<label class="control-label col-md-6">Utilizar servicio de envío de email de Aleph Manager</label>
							<div class="col-md-6">
								<label class="switch">
									<input type="checkbox" id="_notificaciones_email_aleph" name="_notificaciones_email_aleph" value="1"
										<?php if ($notificaciones_email_aleph == 1) {
											echo "checked";
										} ?> />
									<span class="slider round"></span>
								</label>
							</div>
							<?php
							if ($notificaciones_email == 1 && $notificaciones_email_aleph == 1) {
							?>
								<div class="col-md-12"><a href="{{ route('enviarmail') }}" class="btn btn-info"><span class="glyphicon glyphicon-ok"></span> Probar envio de emails</a></div>
							<?php
							}

							?>
						</div>
						<?php
						if ($notificaciones_email == 1 && $notificaciones_email_aleph == 0) {


						?>
							<h4>Configuración del remitente</h4><br>
							<form id="form_config_remitente" action="{{ route('configuracion.guardar_remitente') }}" method="post"><!-- id="form_remitente" enctype="multipart/form-data" class="form-horizontal"-->
								@csrf
								<div class="form-group row">
									<label class="control-label col-md-4">Email*</label>
									<div class="col-md-8">
										<input type="email" name="from" value="<?= $notificaciones_email_from ?>" required placeholder="info@alephmanager.com">
									</div>
								</div>
								<div class="form-group row">
									<label class="control-label col-md-4">Nombre*</label>
									<div class="col-md-8">
										<input type="text" minlength="3" name="from_name" value="<?= $notificaciones_email_from_name ?>" required placeholder="Aleph Manager">
									</div>
								</div>
								<button type="button" id="btn_guardar_remitente" class="btn btn-success">Guardar remitente</button>
							</form>
							<br>
							<h4>Configuración de parametros email</h4>
							<div class="alert alert-warning" role="alert">
								Alterar la configuración puede implicar detener el envio de notificaciones via email, realice la prueba antes de guardar su configuración
							</div>
							<div class="alert alert-info" role="alert">
								Para conocer las configuraciones disponibles de la libreria siga <a class="alert-link" target="_blank" href="#">este enlace</a> y baje hasta la sección con el título "Email Preferences"
							</div>
							<div class="form-group row">
								<a class="btn btn-success" onclick="add_parametro()">Agregar parametro</a>
							</div>
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
											echo '<td><a class="btn btn-danger" onclick="delete_parametro_email(\'' . $parametro . '\')"><i class="glyphicon glyphicon-trash"></i></a></td>';
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
							<form action="{{ route('configuracion.guardar_estado') }}" method="POST">
								@csrf
								@foreach ($variables as $variable)
								@if(Str::startsWith($variable->nombre, 'opav'))
								<div class="form-group row">
									<div class="col-md-6">
										<label>
											<input type="checkbox" id="{{ $variable->nombre }}" name="{{ $variable->nombre }}" value="1" {{ $variable->valor == 1 ? 'checked' : '' }} />
											{{ $variable->nombre_menu }}
										</label>
									</div>
								</div>
								@endif
								@endforeach
							</form>
						</div>
					</div>


					<!-- Acordeón para Configuración de pantallas -->
					<div x-data="{ open5: false }" class="border-t border-gray-200">
						<button @click="open5 = !open5" class="flex justify-between items-center w-full p-4 font-medium text-left text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-gray-500">
							Configuración de pantallas
							<svg :class="{ 'rotate-180': open5, 'rotate-0': !open5 }" class="w-6 h-6 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M5.293 9.707a1 1 0 011.414 0L10 13.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
							</svg>
						</button>
						<div x-show="open5" class="mt-2">
							<!-- Contenido de Configuración de pantallas -->
							<form action="{{ route('configuracion.guardar_estado') }}" method="POST">
								@csrf
								@foreach ($variables as $variable)
								@if(Str::startsWith($variable->nombre, 'copa'))
								<div class="form-group row">
									<div class="col-md-6">
										<label>
											<input type="checkbox" id="{{ $variable->nombre }}" name="{{ $variable->nombre }}" value="1" {{ $variable->valor == 1 ? 'checked' : '' }} />
											{{ $variable->nombre_menu }}
											<span class="slider round"></span>
										</label>
									</div>
								</div>
								<div id="div_{{ $variable->nombre }}" style="display:none">
									<form action="/guardar_imagen_aleph" id="{{ $variable->nombre }}_path" method="post" enctype="multipart/form-data" class="form-horizontal">
										<!-- ver qué hace este form en el aleph de code igniter -->
										<div class="form-group row">
											<label class="control-label col-md-4">Subir imagen</label>
											<div class="col-md-8">
												<input type="file" class="form-control" id="{{ $variable->nombre }}_path" name="{{ $variable->nombre }}_path" accept="image/png, .jpeg, .jpg, .webp, image/gif" required="">
												<span class="help-block"></span>
												<button type="submit" class="btn btn-primary">Guardar</button>
											</div>
										</div>
										<input type="hidden" name="form_token" value="86eefd547e41146d8e40548fd734bc96">
									</form>
								</div>
								<hr>
								@endif
								@endforeach
							</form>

						</div>
					</div>



					<div class="modal fade" id="modal_form_parametro" role="dialog">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title_parametro"></h3>
								</div>
								<div class="modal-body form">
								<?php //route('configuracion/add_parametro_email') ?>
									<form action="{{ route('configuracion.add_parametro_email') }}" id="form_parametro" method="post" enctype="multipart/form-data" class="form-horizontal">
										@csrf
										<div class="form-body">
											<div class="form-group">
												<label class="control-label col-md-3">Parametro</label>
												<div class="col-md-9">
													<input name="parametro" required class="form-control" maxlength="255" type="text">
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-md-3">Valor</label>
												<div class="col-md-9">
													<input name="valor" required class="form-control" maxlength="255" type="text">
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary">Guardar</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>


                    <div id="loadingModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background-color:rgba(0,0,0,0.5); color:white; padding:20px; border-radius:5px;">
                        <p>Cargando...</p>
                    </div>

					<script src="/build/assets/js/jquery-3.6.0.min.js"></script>
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
						document.addEventListener('DOMContentLoaded', function() {
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
									alert('Remitente guardado correctamente');
								},
								error: function(error) {
									alert('Error al guardar el remitente');
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
							<?php //echo route('configuracion/probar_configuracion_email/')?>
							$.ajax({
								url : "<?php echo route('configuracion.index')?>",
								type: "POST",
								dataType: "JSON",
								success: function(data){
														hide_loading();
														swal("Aviso", "La configuración es correcta OK.", "success");
									},
								error: function(jqXHR, textStatus, errorThrown) {
														show_ajax_error_message(jqXHR, textStatus, errorThrown);
									}
								});
						}

						$.ajaxSetup({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							}
						});

						function delete_parametro_email(parametro){
							swal({
								title: "¿Está seguro que desea borrar el parametro?",
								icon: "warning",
								buttons: true,
								dangerMode: true,
							}).then((willProcess) => {
								if (willProcess) {
									console.log($('meta[name="csrf-token"]').attr('content')); // Asegúrate de que no sea `undefined`
									console.log(parametro);
									$.ajax({
										url : "/configuracion/ajax_delete_parametro_email",
										data: {parametro:parametro},
										type: "POST",
										dataType: "JSON",
										headers: {
											'X-CSRF-TOKEN': '{{ csrf_token() }}'
										},
										success: function(data){
											swal("Aviso", "El parametro ha sido eliminado", "success").then(function(){
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


</x-app-layout>