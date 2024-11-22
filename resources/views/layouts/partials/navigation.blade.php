<div id="toolbar-bg">
	<div id="bg-1"></div>
</div>
<style>
	.dropdown-submenu:hover>.dropdown-menu {
		display: none;
	}

	.dropdown:hover>.dropdown-submenu,
	.dropdown-submenu:hover>.dropdown-menu {
		display: block;
		margin-top: .125em;
	}

	.dropdown-submenu:hover>.dropdown-menu {
		position: absolute;
		top: 0;
		left: 100%;
	}

	.ciber_sec_icon {
		font-weight: bold;
		border: 1px solid black;
		padding: 2px;
		color: #cf60ff;
		font-size: 12px;
		box-shadow: black;
		background: #eaeaea;
	}

	.ciber_sec_icon_large {
		font-size: 24px;
		padding: 3px 4px 0px 4px;
	}

	.logo-home-custom {
		float: left;
		margin-right: 20px;
	}

	.logo-home-custom a {
		padding: 0px !important;
		margin: 0px !important;
	}

	.logo-home-custom a img {
		width: 130px;
		max-height: 50px;
	}

	.logo-home-custom a:hover {
		background: none;
		text-decoration: none;
		transform: none;
		transition: none;
	}
</style>
@php
$user = Auth::user()->username;
$email = Auth::user()->email;
$permiso_listar_usuarios = tiene_permiso('list_usr');
$permiso_asignar_permisos = tiene_permiso('manage_perm');
$permiso_listar_roles = tiene_permiso('list_roles');
$permiso_configuraciones_software = tiene_permiso('setup_soft');
@endphp
<nav class="navbar navbar-expand-lg" id="manager-menu">
	<div class="container-fluid">
		<button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#aleph-navbar">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="aleph-navbar">

			<div class="logo-home">
				<a href="{{ url('dashboard') }}">
					<img src="{{ asset('images/aleph_logo.gif') }}" alt="" title="">
				</a>
			</div>

			<ul class="nav navbar-nav me-auto nav-bar-1">
				<li><a class="nav-link main-item" id="boton-inicio" href="{{ url('dashboard') }}">Inicio</a></li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle main-item" data-bs-toggle="dropdown" href="#">Administración
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item hover-aleph-buttons" href="{{ url('permisos_x_rol') }}">Asignación de Permisos</a></li>
						<li><a class="dropdown-item hover-aleph-buttons" href="{{ url('configuracion') }}">Configuraciones</a></li>
						<li><a class="dropdown-item hover-aleph-buttons" href="{{ url('monitoreo') }}">Monitoreo</a></li>
						<li><a class="dropdown-item hover-aleph-buttons" href="{{ url('roles') }}">Roles</a></li>
						<li><a class="dropdown-item hover-aleph-buttons" href="{{ url('users') }}">Usuarios</a></li>
					</ul>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle main-item" data-bs-toggle="dropdown" href="#">Configuración
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item hover-aleph-buttons" href="{{ url('tipos_transacciones') }}">ABM Tipos de transacciones</a></li>
						<li><a class="dropdown-item hover-aleph-buttons" href="{{ url('funciones') }}">ABM de Funciones</a></li>
						<li><a class="dropdown-item hover-aleph-buttons" href="{{ url('conf_alertas') }}">ABM de Alertas</a></li>
					</ul>
				</li>
			</ul>
			<ul id="user-menu-desktop" class="nav navbar-nav nav-bar-2" style="min-width: 188px;">

				<li class="nav-item user-button dropdown">
					<a data-bs-toggle="dropdown" class="nav-link dropdown-toggle main-item" href="#" aria-expanded="true">
						<span class="username"><strong> {{ Auth::user()->nombre }}</strong> </span>
						<span style="font-size: 12px;" class="bi bi-person"></span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu" tabindex="5003" style="overflow: hidden; outline: none;">
						<!--<li>
                            <a class="hover-aleph-buttons" href="">
                                <span class="glyphicon glyphicon-log-in"></span> Cerrar sesión
                            </a>
                        </li>-->
						<li>
							<a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
								class="dropdown-item hover-aleph-buttons cs">
								<i class="bi bi-box-arrow-in-right"></i> Cerrar sesión
							</a>
							<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
								@csrf
							</form>
						</li>
					</ul>
				</li>
			</ul>

		</div>
	</div>
</nav>