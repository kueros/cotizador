<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Contraseña') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-12 bg-white shadow sm:rounded-lg">
            <!-- Formulario para crear una nueva contraseña -->
            <form method="POST" action="{{ route('crear_password') }}">
                @csrf

                <!-- Campo oculto para el token -->
                <input type="hidden" name="token" value="{{ $token }}">
                <!-- Campo oculto para el email -->
                <input type="hidden" name="email" value="{{ $email }}">

                <!-- Nueva Contraseña -->
                <div class="mt-4">
                    <x-input-label for="password" value="{{ __('Ingrese una contraseña que tenga como mínimo 8 caracteres (debe contener: 1 carácter especial, 1 número, 1 letra mayúscula, 1 letra minúscula)') }}" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4" required minlength="8" maxlength="20" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirmar Contraseña -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" value="{{ __('Confirmar Contraseña') }}" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-3/4" required minlength="8" maxlength="20" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    <span id="show_password" class="ms-2 text-sm text-black"><input type="checkbox" value="1" onchange="show_password(this)" /> Mostrar contraseña</span>
                </div>

                <!-- Botón para enviar -->
                <div class="mt-6 flex justify-end">
                    <x-primary-button>
                        {{ __('Crear Nueva Contraseña') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>