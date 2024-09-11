#!/bin/bash

#Este comando debe ejecutarse en la carpeta raiz del proyecto, con el entorno de php activo e inmediatamente despues ejecutar el comando "php artisan migrate".

php -r '
$db = new PDO("mysql:host=127.0.0.1;dbname=yafo_plaft", "root", "root");

$username = "omar";
$nombre = "omar";
$apellido = "omar";
$email = "omarliberatto@yafoconsultora.com";
$password = password_hash("12341234", PASSWORD_DEFAULT);

$sql = "INSERT INTO users (id, username, nombre, apellido, email, password) 
        VALUES (1, :username, :nombre, :apellido, :email, :password)";

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