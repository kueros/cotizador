<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Configuraciones') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard-bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
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
						<div class="form-group row">
							<label class="control-label col-md-6">Utilizar notificaciones locales</label>
							<div class="col-md-6">
								<label class="switch">
									<input name="notificaciones_locales" id="notificaciones_locales" value="1" checked="" onchange="cambiar_configuraciones()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-6">Utilizar notificaciones por email</label>
							<div class="col-md-6">
								<label class="switch">
									<input name="notificaciones_email" id="notificaciones_email" value="1" checked="" onchange="cambiar_configuraciones()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-6">Utilizar servicio de envío de email de Aleph Manager</label>
							<div class="col-md-6">
								<label class="switch">
									<input name="notificaciones_email_default" id="notificaciones_email_default" value="1" checked="" onchange="cambiar_configuraciones()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
							<div class="col-md-12">
								<a onclick="test_config_default(this)" class="btn btn-info">
									<span class="glyphicon glyphicon-ok"></span> Probar envio de emails
								</a>
							</div>
						</div>
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
						<div class="form-group row">
							<label class="control-label col-md-4">Habilitar modo debug</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilitar_modo_debug" id="habilitar_modo_debug" value="1" onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Habilitar IA en módulo de ciberseguridad</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilitar_ia_ciberseguridad" id="habilitar_ia_ciberseguridad" value="1" checked="" onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">OpenAI API Token</label>
							<div class="col-md-8">
								<input class="form-control" placeholder="Ingrese el token y haga clic afuera del campo para guardar" name="open_ai_api_key" id="open_ai_api_key" type="text" onchange="cambiar_configuraciones_avanzadas()">
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Habilitar Módulo de Auditoria</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilita_modulo_auditoria" id="habilita_modulo_auditoria" value="1" onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Acceso módulo de Formulario de encuadramiento</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="acceso_formulario_encuadramiento" id="acceso_formulario_encuadramiento" value="1" onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Acceso módulo de Contratacion de STI</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilita_proceso_contratacion_sti" id="habilita_proceso_contratacion_sti" value="1" onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Habilitar Canales Electrónicos Criticidad de Escenarios</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilita_canales_criticidad_escenarios" id="habilita_canales_criticidad_escenarios" value="1" checked="" onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Habilitar CMDB Corporativa</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="mostrar_cmdb_corporativa" id="mostrar_cmdb_corporativa" value="1" onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
					</div>
				</div>

				<!-- Acordeón para Opciones avanzadas -->
				<div x-data="{ open3: false }" class="border-t border-gray-200">
					<button @click="open3 = !open3" class="flex justify-between items-center w-full p-4 font-medium text-left text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-gray-500">
						Sincronización de ambientes
						<svg :class="{ 'rotate-180': open3, 'rotate-0': !open3 }" class="w-6 h-6 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M5.293 9.707a1 1 0 011.414 0L10 13.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
						</svg>
					</button>
					<div x-show="open3" class="mt-2">
						<!-- Contenido de Sincronización de ambientes -->
						<div class="form-group row">
							<label class="control-label col-md-4">Habilitar sincronización de ambientes</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="sincronizacion_ambiente" id="sincronizacion_ambiente" value="1" onchange="cambiar_configuraciones_sincronizacion(true)" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
					</div>
				</div>

				<!-- Acordeón para Configuración de acceso -->
				<div x-data="{ open4: false }" class="border-t border-gray-200">
					<button @click="open4 = !open4" class="flex justify-between items-center w-full p-4 font-medium text-left text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-gray-500">
						Configuración de acceso
						<svg :class="{ 'rotate-180': open, 'rotate-0': !open }" class="w-6 h-6 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M5.293 9.707a1 1 0 011.414 0L10 13.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
						</svg>
					</button>
					<div x-show="open4" class="mt-2">
						<!-- Contenido de Configuración de acceso -->
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos módulos riesgo IT</label>
							<div class="col-md-4">
								<select class="form-control" name="accesos_modulos_arit" id="accesos_modulos_arit" onchange="acceso_modulo()" style="width:200px;">
									<option value="1" selected="">Todos los accesos</option>
									<option value="2">Acciones pendientes</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos módulos AROP</label>
							<div class="col-md-4">
								<select class="form-control" name="accesos_modulos_arop" id="accesos_modulos_arop" onchange="acceso_modulo()" style="width:200px;">
									<option value="1" selected="">Todos los accesos</option>
									<option value="2">Acciones pendientes</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos módulos BIAS</label>
							<div class="col-md-4">
								<select class="form-control" name="accesos_modulos_bias" id="accesos_modulos_bias" onchange="acceso_modulo()" style="width:200px;">
									<option value="1" selected="">Todos los accesos</option>
									<option value="2">Acciones pendientes</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos módulos Clasificación</label>
							<div class="col-md-4">
								<select class="form-control" name="accesos_modulos_clasificacion" id="accesos_modulos_clasificacion" onchange="acceso_modulo()" style="width:200px;">
									<option value="1" selected="">Todos los accesos</option>
									<option value="2">Acciones pendientes</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos a descarga de archivos (permiso de "Auditoría")</label>
							<div class="col-md-4">
								<select class="form-control" name="acceso_auditor_archivos" id="acceso_auditor_archivos" onchange="cambiar_configuraciones_avanzadas()" style="width:200px;">
									<option value="0">No permitir descargas</option>
									<option value="1" selected="">Permitir descargas</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos a descarga de reportes (permiso de "Auditoría")</label>
							<div class="col-md-4">
								<select class="form-control" name="acceso_auditor_reportes" id="acceso_auditor_reportes" onchange="cambiar_configuraciones_avanzadas()" style="width:200px;">
									<option value="0">No permitir descargas</option>
									<option value="1" selected="">Permitir descargas</option>
								</select>
							</div>
						</div>
					</div>



					<!-- Acordeón para Opciones avanzadas -->
					<div x-data="{ open5: false }" class="border-t border-gray-200">
						<button @click="open5 = !open5" class="flex justify-between items-center w-full p-4 font-medium text-left text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-gray-500">
							Configuración de pantallas
							<svg :class="{ 'rotate-180': open5, 'rotate-0': !open5 }" class="w-6 h-6 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M5.293 9.707a1 1 0 011.414 0L10 13.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
							</svg>
						</button>
						<div x-show="open5" class="mt-2">
							<!-- Contenido de Configuración de pantallas -->
							<div class="form-group row">
								<label class="control-label col-md-4">Utilizar imagen home default</label>
								<div class="col-md-8">
									<label class="switch">
										<input name="background_home_custom" id="background_home_custom" value="1" checked="" onchange="cambiar_configuracion_estilos()" type="checkbox">
										<span class="slider round"></span>
									</label>
								</div>
							</div>
							<div id="div_background_home_custom" style="display:none">
								<form action="https://demo.alephmanager.com/configuracion/guardar_imagen_aleph" id="background_home_custom_path" method="post" enctype="multipart/form-data" class="form-horizontal">
									<div class="form-group row">
										<label class="control-label col-md-4">Subir imagen</label>
										<div class="col-md-8">
											<input type="file" class="form-control" id="background_home_custom_path" name="background_home_custom_path" accept="image/png, .jpeg, .jpg, .webp, image/gif" required="">
											<span class="help-block"></span>
											<button type="submit" class="btn btn-primary">Guardar</button>
										</div>
									</div>
									<input type="hidden" name="form_token" value="86eefd547e41146d8e40548fd734bc96">
								</form>
							</div>
							<hr>
							<div class="form-group row">
								<label class="control-label col-md-4">Utilizar imagen login default</label>
								<div class="col-md-8">
									<label class="switch">
										<input name="background_login_custom" id="background_login_custom" value="1" checked="" onchange="cambiar_configuracion_estilos()" type="checkbox">
										<span class="slider round"></span>
									</label>
								</div>
							</div>
							<div id="div_background_login_custom" style="display:none">
								<form action="https://demo.alephmanager.com/configuracion/guardar_imagen_aleph" id="background_login_custom_path" method="post" enctype="multipart/form-data" class="form-horizontal">
									<div class="form-group row">
										<label class="control-label col-md-4">Subir imagen</label>
										<div class="col-md-8">
											<input type="file" id="background_login_custom_path" name="background_login_custom_path" accept="image/png, .jpeg, .jpg, .webp, image/gif" required="">
											<span class="help-block"></span>
											<button type="submit" class="btn btn-primary">Guardar</button>
										</div>
									</div>
									<input type="hidden" name="form_token" value="86eefd547e41146d8e40548fd734bc96">
								</form>
							</div>
							<hr>
							<div class="form-group row">
								<label class="control-label col-md-4">Utilizar imagen email default</label>
								<div class="col-md-8">
									<label class="switch">
										<input name="background_email_custom" id="background_email_custom" value="1" checked="" onchange="cambiar_configuracion_estilos()" type="checkbox">
										<span class="slider round"></span>
									</label>
								</div>
							</div>
							<div id="div_background_email_custom" style="display:none">
								<form action="https://demo.alephmanager.com/configuracion/guardar_imagen_aleph" id="background_email_custom_path" method="post" enctype="multipart/form-data" class="form-horizontal">
									<div class="form-group row">
										<label class="control-label col-md-4">Subir imagen</label>
										<div class="col-md-8">
											<input type="file" id="background_email_custom_path" name="background_email_custom_path" accept="image/png, .jpeg, .jpg, .webp, image/gif" required="">
											<span class="help-block"></span>
											<button type="submit" class="btn btn-primary">Guardar</button>
										</div>
									</div>
									<input type="hidden" name="form_token" value="86eefd547e41146d8e40548fd734bc96">
								</form>
							</div>
							<hr>
							<div class="form-group row">
								<label class="control-label col-md-4">Ocultar leyenda</label>
								<div class="col-md-8">
									<label class="switch">
										<input name="leyenda_ambiente" id="leyenda_ambiente" value="1" checked="" onchange="cambiar_configuracion_estilos()" type="checkbox">
										<span class="slider round"></span>
									</label>
								</div>
							</div>
							<div id="div_leyenda_ambiente" style="display:none">
								<form action="https://demo.alephmanager.com/configuracion/guardar_leyenda" id="leyenda_ambiente" method="post" enctype="multipart/form-data" class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-md-4">Color fondo de leyenda</label>
										<div class="col-md-8">
											<input type="color" name="leyenda_ambiente_color" required="" id="leyenda_ambiente_color" value="#FFFFFF" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-4">Texto de leyenda</label>
										<div class="col-md-8">
											<input type="text" name="leyenda_ambiente_texto" required="" id="leyenda_ambiente_texto" class="form-control" value="">
											<span class="help-block"></span>
											<button type="submit" class="btn btn-primary">Guardar</button>
										</div>
									</div>
									<input type="hidden" name="form_token" value="86eefd547e41146d8e40548fd734bc96">
								</form>
							</div>
							<hr>
							<div class="form-group row">
								<label class="control-label col-md-4">Utilizar logotipo de Aleph Manager default</label>
								<div class="col-md-8">
									<label class="switch">
										<input name="aleph_estilo_logotipo_default" id="aleph_estilo_logotipo_default" value="1" checked="" onchange="cambiar_configuracion_estilos()" type="checkbox">
										<span class="slider round"></span>
									</label>
								</div>
							</div>
							<div id="div_aleph_estilo_logotipo_default" style="display:none">
								<form action="https://demo.alephmanager.com/configuracion/guardar_imagen_aleph" id="aleph_estilo_logotipo_custom_path" method="post" enctype="multipart/form-data" class="form-horizontal">
									<div class="form-group row">
										<label class="control-label col-md-4">Subir imagen</label>
										<div class="col-md-8">
											<input type="file" id="aleph_estilo_logotipo_custom_path" name="aleph_estilo_logotipo_custom_path" accept="image/png, .jpeg, .jpg, .webp, image/gif" required="">
											<span class="help-block"></span>
											<button type="submit" class="btn btn-primary">Guardar</button>
										</div>
									</div>
									<input type="hidden" name="form_token" value="86eefd547e41146d8e40548fd734bc96">
								</form>
							</div>
							<hr>
							<div class="form-group row">
								<label class="control-label col-md-4">Utilizar color de barra del menú default</label>
								<div class="col-md-8">
									<label class="switch">
										<input name="aleph_estilo_menu_default" id="aleph_estilo_menu_default" value="1" checked="" onchange="cambiar_configuracion_estilos()" type="checkbox">
										<span class="slider round"></span>
									</label>
								</div>
							</div>
							<div id="div_aleph_estilo_menu_default" style="display:none">
								<form action="https://demo.alephmanager.com/configuracion/guardar_colores_barra_menu" method="post" enctype="multipart/form-data" class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-md-3">Color de la barra del menú</label>
										<div class="col-md-9">
											<input type="color" name="aleph_estilo_color_barra_menu" required="" id="aleph_estilo_color_barra_menu" value="#0A1638" class="form-control" onchange="cambiar_configuracion_estilos()">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Color de los títulos en el menú</label>
										<div class="col-md-9">
											<input type="color" name="aleph_estilo_color_titulos_menu" required="" id="aleph_estilo_color_titulos_menu" value="#FFFFFF" class="form-control" onchange="cambiar_configuracion_estilos()">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Color de mouseover en el menú</label>
										<div class="col-md-9">
											<input type="color" name="aleph_estilo_color_mouseover_menu" required="" id="aleph_estilo_color_mouseover_menu" value="#F5F5F5" class="form-control" onchange="cambiar_configuracion_estilos()">
										</div>
									</div>
									<input type="hidden" name="form_token" value="86eefd547e41146d8e40548fd734bc96">
								</form>
							</div>
							<hr>
						</div>
					</div>


				</div>
			</div>


</x-app-layout>