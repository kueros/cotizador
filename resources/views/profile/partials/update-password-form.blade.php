<x-app-layout>

@section('content')
<div class="container">
    <h2>Cambiar Contraseña</h2>
    <form action="{{ route('password.update', ['userId' => $userId]) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="password">Nueva Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <small class="form-text text-muted">Debe contener al menos 8 caracteres, incluyendo 1 número, 1 letra mayúscula, 1 letra minúscula y 1 carácter especial.</small>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmar Contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
    </form>
</div>

<script>
    document.getElementById('password').addEventListener('input', function () {
        const password = this.value;
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/;
        if (!regex.test(password)) {
            this.setCustomValidity('La contraseña debe tener al menos 8 caracteres, con 1 número, 1 mayúscula, 1 minúscula y 1 carácter especial.');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
</x-app-layout>
