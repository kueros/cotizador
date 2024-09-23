<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
	<!-- Primary Navigation Menu -->
	@php
	$user = Auth::user()->username;
	$email = Auth::user()->email;
	@endphp
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex justify-between h-16">
			<div class="flex">
				<!-- Logo -->
				<div class="shrink-0 flex items-center">
					<a href="{{ route('dashboard') }}">
						<img class="logo-home" src="/build/assets/images/log_accesos.png" width="50" height="50" alt="" title="">
					</a>
				</div>

				<!-- Navigation Links -->
				<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
					<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
						{{ __('Inicio') }}
					</x-nav-link>

					<div class="hidden sm:flex sm:items-center sm:ms-12">
						<x-dropdown align="left" width="48">
							<x-slot name="trigger">
								<button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
									<div>Administración</div>
									<div class="ms-1">
										v
										<span class="caret"></span>
									</div>
								</button>
							</x-slot>

							<x-slot name="content">
								<x-dropdown-link :href="route('profile.edit')">
									Permisos
								</x-dropdown-link>
								<x-dropdown-link :href="route('roles.index')">
									Roles
								</x-dropdown-link>
								<x-dropdown-link :href="route('users.index')">
									Usuarios
								</x-dropdown-link>
								<x-dropdown-link :href="route('monitoreo.index')">
									Monitoreo
								</x-dropdown-link>
								<x-dropdown-link :href="route('configuracion.index')">
									Configuración
								</x-dropdown-link>
								<x-dropdown-link :href="route('enviarmail')">
									Mails
								</x-dropdown-link>
							</x-slot>
						</x-dropdown>
					</div>

				</div>
			</div>


			<!-- Settings Dropdown -->
			<div class="hidden sm:flex sm:items-center sm:ms-6">
				<x-dropdown align="right" width="48">
					<x-slot name="trigger">
						<button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
							<div>{{ Auth::user()->username }}</div>

							<div class="ms-1">
								v
								<span class="caret"></span>
							</div>
						</button>
					</x-slot>

					<x-slot name="content">
						<x-dropdown-link :href="route('profile.edit')">
							{{ __('Profile') }}
						</x-dropdown-link>

						<!-- Authentication -->
						<form method="POST" action="url('logout')">
							@csrf

							<x-dropdown-link :href="url('logout')"
								onclick="event.preventDefault();
                                                this.closest('form').submit();">
								{{ __('Log Out') }}
							</x-dropdown-link>
						</form>
					</x-slot>
				</x-dropdown>
			</div>

			<!-- Hamburger -->
			<div class="-me-2 flex items-center sm:hidden">
				<button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
					<svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
						<path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
						<path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
			</div>
		</div>
	</div>

	<!-- Responsive Navigation Menu -->
	<div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
		<div class="pt-2 pb-3 space-y-1">
			<x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
				{{ __('Dashboard') }}
			</x-responsive-nav-link>
		</div>

		<!-- Responsive Settings Options -->
		<div class="pt-4 pb-1 border-t border-gray-200">
			<div class="px-4">
				<div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
				<div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
			</div>

			<div class="mt-3 space-y-1">
				<x-responsive-nav-link :href="route('profile.edit')">
					{{ __('Profile') }}
				</x-responsive-nav-link>

				<!-- Authentication -->
				<form method="POST" action="url('logout')">
					@csrf
					<input type="hidden" name="email" value="{{ $email }}">
					<input type="hidden" name="username" value="{{ $user }}">
					<x-responsive-nav-link :href="url('logout')"
						onclick="event.preventDefault();
                                        this.closest('form').submit();">
						{{ __('Log Out') }}
					</x-responsive-nav-link>
				</form>
			</div>
		</div>
	</div>
</nav>