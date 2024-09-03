<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Dashboard') }}
		</h2>
	</x-slot>

	<div style="background-image: url('/public/build/assets/images/dashboard-bg.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
		<div class="py-12" style="padding-bottom: 40rem;">
			<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
				<div class="bg-white bg-opacity-75 overflow-hidden shadow-sm sm:rounded-lg">
					<div class="p-6 text-gray-900">
						{{ __("You're logged in!") }}
					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>
