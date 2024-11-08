<x-app-layout title="Permiso denegado" :breadcrumbs="[['title' => 'Inicio', 'url' => 'dashboard']]">
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			Permiso denegado
		</h2>
	</x-slot>

	<style>
    .mensaje-centrado{
        text-align: center;
    }
	</style>
	<div class="container-full-width">
		<div class="mensaje-centrado">
			<h1>🚫 Acceso denegado 🚫</h1>
			<h3>No tienes permiso para realizar esta acción</h3>
			<hr>
			<p><h3></h3></p>
		</div>
	</div>

</x-app-layout>
