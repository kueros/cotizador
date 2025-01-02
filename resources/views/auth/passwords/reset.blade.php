<form method="POST" action="{{ route('users.update_password') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="password" name="password" placeholder="Nueva contraseña">
    <button type="submit">Cambiar contraseña</button>
</form>