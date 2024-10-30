<!DOCTYPE html>
<html>
<head>
    <title>Blanqueo de Contraseña</title>
</head>
<body>
    <p>Estimado {{ $user->nombre }},</p>
    <p>Se ha realizado un blanqueo de su contraseña en Aleph Manager. Para continuar y cambiar su contraseña, siga el siguiente enlace:</p>
    <p><a href="{{ $url }}">Haz clic aquí</a></p>
    <p>Si no solicitó este cambio, por favor ignore este correo.</p>
    <p>Atentamente,<br>Aleph Manager</p>
</body>
</html>