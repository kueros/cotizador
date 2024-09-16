#!/bin/bash

#Este comando debe ejecutarse en la carpeta raiz del proyecto, con el entorno de php activo e inmediatamente despues ejecutar el comando "php artisan migrate".
# Agregar posibilidad de ejecutar el comando con parametros para que tome los datos desde la linea de comandos.

php -r '
$db = new PDO("mysql:host=127.0.0.1;dbname=yafo_plaft", "root", "root");

$username = "'$1'";
$nombre = "'$2'";
$apellido = "'$3'";
$rol_id = 1;
$email = "'$4'";
$password = password_hash("12341234", PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, nombre, apellido, email, password) 
        VALUES (:username, :nombre, :apellido, :email, :password)";

$stmt = $db->prepare($sql);
$stmt->execute([
    ":username" => $username,
    ":nombre" => $nombre,
    ":apellido" => $apellido,
    ":email" => $email,
    ":password" => $password
]);

if ($stmt->rowCount() > 0) {
    echo "Usuario principal creado con éxito.\n";
} else {
    echo "Error al crear el usuario principal.\n";
}
'