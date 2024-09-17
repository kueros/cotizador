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
						<form action="{{ route('guardarestado') }}" method="POST">
							@csrf
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
					</div>
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
						<form action="{{ route('guardarestado') }}" method="POST">
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
						<form action="{{ route('guardarestado') }}" method="POST">
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
								<form action="https://demo.alephmanager.com/configuracion/guardar_imagen_aleph" id="{{ $variable->nombre }}_path" method="post" enctype="multipart/form-data" class="form-horizontal">
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

				<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

				<script>
					$('input[type="checkbox"]').on('change', function() {
						// Obtenemos el nombre del checkbox que ha sido clicado
						let variableName = $(this).attr('name');

						// Verificamos si está chequeado o no
						let isChecked = $(this).is(':checked');

						// Obtenemos el div correspondiente basado en el id del checkbox
						let targetDiv = $('#div_' + variableName);

						// Si está chequeado, mostramos el formulario; si no, lo ocultamos
						if (isChecked) {
							targetDiv.slideUp(); // Ocultar el formulario
						} else {
							targetDiv.slideDown(); // Mostrar el formulario
						}

						// Enviamos la petición AJAX para guardar el estado
						$.ajax({
							url: '{{ route("guardarestado") }}',
							type: 'POST',
							data: {
								_token: '{{ csrf_token() }}',
								[variableName]: isChecked ? 1 : 0
							},
							dataType: "JSON",
							success: function(response) {
								alert('Estado guardado correctamente');
							},
							error: function(error) {
								alert('Error al guardar el estado', error);
							}
						});
					});
				</script>


</x-app-layout>
