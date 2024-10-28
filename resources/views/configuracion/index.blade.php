<x-app-layout title="Configuración" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ __('Configuraciones') }}
		</h2>
	</x-slot>
	
	<div class="container">
		<div class="row">
			<div class="col-md-6"><h2>breadcumb_deshabilitado</h2></div>
		</div>

		<div class="accordion" id="accordionConfiguraciones">
			<div class="accordion-item">
				<h2 class="accordion-header" id="headingOne">
					<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
						data-bs-target="#email" aria-expanded="true" aria-controls="collapseOne">
						Notificaciones
					</button>
				</h2>
				<div id="email" class="accordion-collapse collapse" aria-labelledby="headingOne" 
					data-bs-parent="#accordionConfiguraciones">
					<div class="accordion-body">
						<br>
						<div class="form-group row">
							<label class="control-label col-md-6">Utilizar notificaciones locales</label>
							<div class="col-md-6">
								<label class="switch">
									<input name="notificaciones_locales" id="notificaciones_locales" value="1"  onchange="cambiar_configuraciones()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-6">Utilizar notificaciones por email</label>
							<div class="col-md-6">
								<label class="switch">
									<input name="notificaciones_email" id="notificaciones_email" value="1"  onchange="cambiar_configuraciones()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-6">Utilizar servicio de envío de email de Aleph Manager</label>
							<div class="col-md-6">
								<label class="switch">
									<input name="notificaciones_email_default" id="notificaciones_email_default" value="1" onchange="cambiar_configuraciones()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
							
						</div>
						
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
						<div class="form-group row">
							<label class="control-label col-md-4">Habilitar modo debug</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilitar_modo_debug" id="habilitar_modo_debug" value="1"  onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Habilitar IA en módulo de ciberseguridad</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilitar_ia_ciberseguridad" id="habilitar_ia_ciberseguridad" value="1"  onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">OpenAI API Token</label>
							<div class="col-md-8">
								<input class="form-control" placeholder="Ingrese el token y haga clic afuera del campo para guardar" name="open_ai_api_key" id="open_ai_api_key" type="text" onchange="cambiar_configuraciones_avanzadas()" />
							</div>
						</div>
						<div class="form-group row" style="">
							<label class="control-label col-md-4">Habilitar Módulo de Auditoria </label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilita_modulo_auditoria" id="habilita_modulo_auditoria" value="1"  onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row" style="">
							<label class="control-label col-md-4">Acceso módulo de Formulario de encuadramiento</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="acceso_formulario_encuadramiento" id="acceso_formulario_encuadramiento" value="1" onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row" style="">
							<label class="control-label col-md-4">Acceso módulo de Contratacion de STI</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilita_proceso_contratacion_sti" id="habilita_proceso_contratacion_sti" value="1"  onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row" style="">
							<label class="control-label col-md-4">Habilitar Canales Electrónicos Criticidad de Escenarios</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilita_canales_criticidad_escenarios" id="habilita_canales_criticidad_escenarios" value="1"  onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row" style="">
							<label class="control-label col-md-4">Habilitar Encuestas de Control Proveedores</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="habilita_encuesta_control_proveedores" id="habilita_encuesta_control_proveedores" value="1"  onchange="cambiar_configuraciones_avanzadas()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div class="form-group row" style="">
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
			</div>
		
			<div class="accordion-item">
				<h2 class="accordion-header" id="headingOne">
					<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
						data-bs-target="#configuracion_acceso" aria-expanded="true" aria-controls="collapseOne">
						Configuración de acceso
					</button>
				</h2>
				<div id="configuracion_acceso" class="accordion-collapse collapse" aria-labelledby="headingOne" 
					data-bs-parent="#accordionConfiguraciones">
					<div class="accordion-body">
						<br>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos módulos riesgo IT</label>
							<div class="col-md-4">
								<select class="form-control" name="accesos_modulos_arit" id="accesos_modulos_arit" onchange="acceso_modulo()" style="width:200px;">
									<option value="1" >Todos los accesos</option>
									<option value="2" >Acciones pendientes</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos módulos AROP</label>
							<div class="col-md-4">
								<select class="form-control" name="accesos_modulos_arop" id="accesos_modulos_arop" onchange="acceso_modulo()" style="width:200px;">
									<option value="1" >Todos los accesos</option>
									<option value="2" >Acciones pendientes</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos módulos BIAS</label>
							<div class="col-md-4">
								<select class="form-control" name="accesos_modulos_bias" id="accesos_modulos_bias" onchange="acceso_modulo()" style="width:200px;">
									<option value="1" >Todos los accesos</option>
									<option value="2" >Acciones pendientes</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos módulos Clasificación</label>
							<div class="col-md-4">
								<select class="form-control" name="accesos_modulos_clasificacion" id="accesos_modulos_clasificacion" onchange="acceso_modulo()" style="width:200px;">
									<option value="1" >Todos los accesos</option>
									<option value="2" >Acciones pendientes</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos a descarga de archivos (permiso de "Auditoría")</label>
							<div class="col-md-4">
								<select class="form-control" name="acceso_auditor_archivos" id="acceso_auditor_archivos" onchange="cambiar_configuraciones_avanzadas()" style="width:200px;">
									<option value="0" >No permitir descargas</option>
									<option value="1" >Permitir descargas</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="control-label col-md-4">Accesos a descarga de reportes (permiso de "Auditoría")</label>
							<div class="col-md-4">
								<select class="form-control" name="acceso_auditor_reportes" id="acceso_auditor_reportes" onchange="cambiar_configuraciones_avanzadas()" style="width:200px;">
									<option value="0" >No permitir descargas</option>
									<option value="1" >Permitir descargas</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>

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
						<div class="form-group row">
							<label class="control-label col-md-4">Utilizar imagen home default</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="background_home_custom" id="background_home_custom" value="1"  onchange="cambiar_configuracion_estilos()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div id="div_background_home_custom" style="">
							<form action="" id="background_home_custom_path" method="post" enctype="multipart/form-data" class="form-horizontal">
								<div class="form-group row">
									<label class="control-label col-md-4">Subir imagen</label>
									<div class="col-md-8">
										<input type="file" class="form-control" id="background_home_custom_path" name="background_home_custom_path" accept="image/png, .jpeg, .jpg, .webp, image/gif" required>
										<span class="help-block"></span>
										<button type="submit" class="btn btn-primary">Guardar</button>
									</div>
								</div>
							</form>
						</div>
						<hr>
						<div class="form-group row">
							<label class="control-label col-md-4">Utilizar imagen login default</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="background_login_custom" id="background_login_custom" value="1"  onchange="cambiar_configuracion_estilos()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div id="div_background_login_custom" style="">
						<form action="" id="background_login_custom_path" method="post" enctype="multipart/form-data" class="form-horizontal">
						<div class="form-group row">
								<label class="control-label col-md-4">Subir imagen</label>
								<div class="col-md-8">
									<input type="file" id="background_login_custom_path" name="background_login_custom_path" accept="image/png, .jpeg, .jpg, .webp, image/gif" required>
									<span class="help-block"></span>
									<button type="submit" class="btn btn-primary">Guardar</button>
								</div>
							</div>
						</form>
						</div>
						<hr>
						<div class="form-group row">
							<label class="control-label col-md-4">Utilizar imagen email default</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="background_email_custom" id="background_email_custom" value="1"  onchange="cambiar_configuracion_estilos()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div id="div_background_email_custom" style="">
							<form action="" id="background_email_custom_path" method="post" enctype="multipart/form-data" class="form-horizontal">
								<div class="form-group row">
									<label class="control-label col-md-4">Subir imagen</label>
									<div class="col-md-8">
										<input type="file" id="background_email_custom_path" name="background_email_custom_path" accept="image/png, .jpeg, .jpg, .webp, image/gif" required>
										<span class="help-block"></span>
										<button type="submit" class="btn btn-primary">Guardar</button>
									</div>
								</div>
							</form>
						</div>
						<hr>
						<div class="form-group row">
							<label class="control-label col-md-4">Ocultar leyenda</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="leyenda_ambiente" id="leyenda_ambiente" value="1"  onchange="cambiar_configuracion_estilos()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div id="div_leyenda_ambiente" style="">
							<form action="" id="leyenda_ambiente" method="post" enctype="multipart/form-data" class="form-horizontal">
								<div class="form-group">
									<label class="control-label col-md-4">Color fondo de leyenda</label>
									<div class="col-md-8">
										<input type="color" name="leyenda_ambiente_color" required id="leyenda_ambiente_color" value="" class="form-control"/>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-4">Texto de leyenda</label>
									<div class="col-md-8">
										<input type="text" name="leyenda_ambiente_texto" required id="leyenda_ambiente_texto" class="form-control" value=""/>
										<span class="help-block"></span>
										<button type="submit" class="btn btn-primary">Guardar</button>
									</div>
								</div>
							</form>
						</div>
						<hr>
						<div class="form-group row">
							<label class="control-label col-md-4">Utilizar logotipo de Aleph Manager default</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="aleph_estilo_logotipo_default" id="aleph_estilo_logotipo_default" value="1"  onchange="cambiar_configuracion_estilos()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div id="div_aleph_estilo_logotipo_default" style="">
							<form action="" id="aleph_estilo_logotipo_custom_path" method="post" enctype="multipart/form-data" class="form-horizontal">
								<div class="form-group row">
									<label class="control-label col-md-4">Subir imagen</label>
									<div class="col-md-8">
										<input type="file" id="aleph_estilo_logotipo_custom_path" name="aleph_estilo_logotipo_custom_path" accept="image/png, .jpeg, .jpg, .webp, image/gif" required>
										<span class="help-block"></span>
										<button type="submit" class="btn btn-primary">Guardar</button>
									</div>
								</div>
							</form>
						</div>
						<hr>
						<div class="form-group row">
							<label class="control-label col-md-4">Utilizar color de barra del menú default</label>
							<div class="col-md-8">
								<label class="switch">
									<input name="aleph_estilo_menu_default" id="aleph_estilo_menu_default" value="1"  onchange="cambiar_configuracion_estilos()" type="checkbox">
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div id="div_aleph_estilo_menu_default" style="">
							<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
								<div class="form-group">
									<label class="control-label col-md-3">Color de la barra del menú</label>
									<div class="col-md-9">
										<input type="color" name="aleph_estilo_color_barra_menu" required id="aleph_estilo_color_barra_menu" value="" class="form-control" onchange="cambiar_configuracion_estilos()"/>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Color de los títulos en el menú</label>
									<div class="col-md-9">
										<input type="color" name="aleph_estilo_color_titulos_menu" required id="aleph_estilo_color_titulos_menu" value="" class="form-control" onchange="cambiar_configuracion_estilos()"/>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Color de mouseover en el menú</label>
									<div class="col-md-9">
										<input type="color" name="aleph_estilo_color_mouseover_menu" required id="aleph_estilo_color_mouseover_menu" value="" class="form-control" onchange="cambiar_configuracion_estilos()"/>
									</div>
								</div>
							</form>
						</div>
						<hr>
					</div>	
				</div>

			</div>

		</div>

	</div>
	
</x-app-layout>