<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Usuarios') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard-bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">

		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="max-w-xl">
					<div class="card">
						<div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
							<div class="float-left">
								<p class="float-left"><strong>MOSTRAR USUARIOS</strong></p><br />
								<a class="btn btn-warning btn-sm card-title" href="{{ route('users.index') }}"> {{ __('Volver') }}</a>
							</div>
						</div>
					</div>

					<div class="card-body bg-white">

						<div class="form-group mb-2 mb20">
							<strong>Nombre de Usuario:</strong>
							{{ $user->username }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Nombre:</strong>
							{{ $user->nombre }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Apellido:</strong>
							{{ $user->apellido }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Habilitado:</strong>
							{{ $user->habilitado }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Eliminado:</strong>
							{{ $user->fecha_eliminado }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Bloqueado:</strong>
							{{ $user->bloqueado }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Ultimo Login:</strong>
							{{ $user->ultimo_login }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Intentos de Login:</strong>
							{{ $user->intentos_login }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Fecha de Creacion:</strong>
							{{ $user->created_at }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Fecha de Modificacion:</strong>
							{{ $user->updated_at }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Fecha de Eliminacion:</strong>
							{{ $user->deleted_at }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Email:</strong>
							{{ $user->email }}
						</div>
						<div class="form-group mb-2 mb20">
							<strong>Estado:</strong>
							{{ $user->estado }}
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>