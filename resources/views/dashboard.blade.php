<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Dashboard') }}
		</h2>
	</x-slot>

<?php 

// $copa_background_home_custom = \Variable::where('nombre', 'copa_background_home_custom')->first()['valor'];
//$background_home_custom_path = \Variable::where('nombre', 'background_home_custom_path')->first()['valor'];
?>
    <style>

		<?php 
		#$copa_background_login_custom = 1;
		if($copa_background_home_custom == '0'){
		?>
			body, html {
			height: 100%;
			background-repeat: no-repeat;
			background: url('{{ asset($background_home_custom_path) }}')  center top no-repeat;
			background-repeat: no-repeat;
			background-size: cover;
			}
		<?php 
		}else{
			?>
			body, html {
				height: 100%;
				background-repeat: no-repeat;
				background: url(/images/dashboard-bg.jpg) center top no-repeat;
				background-repeat: no-repeat;
				background-size: cover;
			}
		<?php
		}
		?>


	</style>

	<div id="dashboard">
		<div id="dashboard-content">
		</div>
	</div>
</x-app-layout>
