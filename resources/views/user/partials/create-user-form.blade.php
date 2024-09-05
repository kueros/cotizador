<section>
	<header>

		<div class="container-full-width">
			<div class="row">
				<div class="col-md-12">
					<h2>Usuarios</h2>
					<a data-toggle="collapse" data-target="#opciones-usuario">
						<div class="colapsable-aleph"><span class="glyphicon glyphicon-chevron-right"></span> Opciones</div>
					</a>
					<div id="opciones-usuario" class="collapse">
						<form action="{{ route('users.options') }}" method="post">
							<?php
							if (!isset($_SESSION['azure'])) {
								$reset_password = true;
								$configurar_claves = true;
								$permitir_multiples_sesiones = true;
								$session_time = 3600;
								$filtrar_usuarios = 0;
							?>
								<div class="row">
									<div class="form-body">
										<div class="form-group">
											<label class="control-label col-md-3">Requerir cambio de contraseña después de 30 días</label>
											<div class="col-md-9">
												<input type="checkbox" value="1" <?php if ($reset_password) {
																						echo 'checked';
																					} ?> name="request_change" id="request_change">
												<span class="help-block"></span>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-body">
										<div class="form-group">
											<label class="control-label col-md-3">Configurar contraseñas</label>
											<div class="col-md-3">
												<input type="checkbox" value="1" <?php if ($configurar_claves) {
																						echo 'checked';
																					} ?> name="configurar_claves" id="configurar_claves">
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-body">
										<div class="form-group">
											<label class="control-label col-md-3">Permitir múltiples sesiones</label>
											<div class="col-md-3">
												<input type="checkbox" value="1" <?php if ($permitir_multiples_sesiones) {
																						echo 'checked';
																					} ?> name="permitir_multiples_sesiones" id="permitir_multiples_sesiones">
											</div>
										</div>
									</div>
								</div>
							<?php
							}
							?>
							<div class="row">
								<div class="form-body">
									<div class="form-group">
										<label class="control-label col-md-3">Tiempo de sesión (segundos)</label>
										<div class="col-md-3">
											<input class="form-control" name="session_time" type="number" min="60" max="864000" step="1" value="<?= $session_time ?>" />
										</div>
									</div>
								</div>
							</div>
							<br>
							<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar opciones</button>
						</form>
						<hr>
					</div>
					<button id="agregar" class="btn btn-success" onclick="add_usuario()"><i class="glyphicon glyphicon-plus"></i> Agregar usuario</button>
					<button id="importar" class="btn btn-primary" onclick="importar_usuarios()"><i class="glyphicon glyphicon-import"></i> Importar usuarios</button>
					<a class="btn btn-info" href="{{ route('users.fields') }}"><i class="glyphicon glyphicon-th-list"></i> Campos adicionales</a>
					<br>
					<br>
					<div class="form-group">
						<label class="control-label">Filtro de usuarios</label>
						<select class="form-control" name="filtrar_usuarios" id="filtrar_usuarios" onchange="alert('filtrar_usuarios()')" style="width:200px;">
							<option value="0" <?php if ($filtrar_usuarios == 0) {
													echo 'selected';
												} ?>>Solo habilitados</option>
							<option value="1" <?php if ($filtrar_usuarios == 1) {
													echo 'selected';
												} ?>>Todos</option>
							<option value="2" <?php if ($filtrar_usuarios == 2) {
													echo 'selected';
												} ?>>Eliminados</option>
						</select>
					</div>
					<br>
					<div class="table-responsive">
						<table id="usuarios-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
							<thead>
								<?php
								if (!empty($campos_usuarios)) {
									foreach ($campos_usuarios as $campo) {
										if ($campo->nombre_campo == 'password') {
											continue;
										}
										if ($campo->visible) {
											echo '<th>' . $campo->nombre_mostrar . '</th>';
										}
									}
								}
								?>
								<th style="width:125px;">Acción</th>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</header>

	<form method="post" action="{{ route('users.create') }}" class="mt-6 space-y-6">
		@csrf
		@method('get')

		<div>
			<x-input-label for="username" :value="__('Nombre de Usuario')" />
			<x-text-input id="username" value="{{ $user->username }}" name="username" type="text" class="mt-1 block w-full" autocomplete="username" />
			<x-input-error :messages="$errors->updatePassword->get('username')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="nombre" :value="__('Nombre')" />
			<x-text-input id="nombre" value="{{ $user->nombre }}" name="nombre" type="text" class="mt-1 block w-full" autocomplete="nombre" />
			<x-input-error :messages="$errors->updatePassword->get('nombre')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="apellido" :value="__('Apellido')" />
			<x-text-input id="apellido" value="{{ $user->apellido }}" name="apellido" type="text" class="mt-1 block w-full" autocomplete="apellido" />
			<x-input-error :messages="$errors->updatePassword->get('apellido')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="email" :value="__('Email')" />
			<x-text-input id="email" value="{{ $user->email }}" name="email" type="text" class="mt-1 block w-full" autocomplete="email" />
			<x-input-error :messages="$errors->updatePassword->get('email')" class="mt-2" />
		</div>

		<div>
			<x-input-label for="habilitado" :value="__('Habilitado')" />
			<x-text-input id="habilitado" value="{{ $user->habilitado }}" name="habilitado" type="text" class="mt-1 block w-full" autocomplete="habilitado" />
			<x-input-error :messages="$errors->updatePassword->get('habilitado')" class="mt-2" />
		</div>

		<div class="flex items-center gap-4">
			<x-primary-button>{{ __('Save') }}</x-primary-button>

			@if (session('status') === 'profile-updated')
			<p
				x-data="{ show: true }"
				x-show="show"
				x-transition
				x-init="setTimeout(() => show = false, 2000)"
				class="text-sm text-gray-600">{{ __('Saved.') }}</p>
			@endif
		</div>
	</form>
</section>