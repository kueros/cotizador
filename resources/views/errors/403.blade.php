<x-app-layout>


	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Error') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/build/assets/images/dashboard_bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; padding-top: 3rem; padding-bottom: 3rem;">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="p-12 sm:p-8 bg-white shadow sm:rounded-lg">
				<div class="lock"></div>
				<div class="message ">
					<h1 class="error403 uppercase font-semibold text-xl">El acceso a esta página está restringido</h1>
							<h2 class="text-xl text-gray-800 leading-tight" style="padding: 20px;">
								Por favor, comuníquese con el administrador de su App si cree que esto es un error.
							</h2>
				</div>
			</div>
		</div>
	</div>


</x-app-layout>