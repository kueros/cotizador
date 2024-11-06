<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Dashboard') }}
		</h2>
	</x-slot>

<?php 
$imagenHome = session('imagenHome')->valor ?? 'images/dashboard-bg.jpg';

?>
    <style>
		#dashboard{
			background: url( <?php echo $imagenHome; ?> );
			background-repeat: none;
			background-size: cover;
			position: absolute;
			width: 100%;
			top: 0;
			min-height: 100%;
			z-index: -9999;
		}
	</style>

	<div id="dashboard">
		<div id="dashboard-content">
		</div>
	</div>
</x-app-layout>
