<?php

// Parámetros para la base de datos de origen
$sourceHost = '127.0.0.1';
$sourceDB = 'yafo_plaft';
$sourceUser = 'root';
$sourcePass = 'root';


// Parámetros para la base de datos de destino
$targetHost = '127.0.0.1';
$targetDB = 'yafo_plaft2';
$targetUser = 'root';
$targetPass = 'root';

// Conectar a la base de datos de origen
try {
    $sourcePDO = new PDO("mysql:host=$sourceHost;dbname=$sourceDB", $sourceUser, $sourcePass);
    $sourcePDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conectado a la base de datos de origen\n";
} catch (PDOException $e) {
    die("Error al conectar a la base de datos de origen: " . $e->getMessage());
}

// Conectar a la base de datos de destino
try {
    $targetPDO = new PDO("mysql:host=$targetHost;dbname=$targetDB", $targetUser, $targetPass);
    $targetPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conectado a la base de datos de destino\n";
} catch (PDOException $e) {
    die("Error al conectar a la base de datos de destino: " . $e->getMessage());
}

// Obtener la estructura de las tablas 'users', 'roles', 'permisos', y 'roles_x_usuario'
$tables = ['users', 'roles', 'permisos', 'roles_x_usuario'];

foreach ($tables as $table) {
    echo "Generando estructura para la tabla: $table\n";

    // Obtener la estructura de la tabla desde la base de datos de origen
    $createTableSQL = $sourcePDO->query("SHOW CREATE TABLE $table")->fetch(PDO::FETCH_ASSOC);
    $createTableQuery = $createTableSQL['Create Table'];

    // Ejecutar el SQL de creación en la base de datos de destino
    try {
        $targetPDO->exec("DROP TABLE IF EXISTS $table"); // Eliminar si ya existe en la base de datos de destino
        $targetPDO->exec($createTableQuery); // Crear la tabla en la base de datos de destino
        echo "Tabla '$table' creada correctamente en la base de datos de destino.\n";
    } catch (PDOException $e) {
        echo "Error al crear la tabla '$table': " . $e->getMessage() . "\n";
    }
}

// Función para copiar los datos de la tabla origen a la tabla destino
function copiarDatos($sourcePDO, $targetPDO, $table)
{
    echo "Copiando datos de la tabla $table...\n";

    // Obtener los datos de la tabla de origen
    $data = $sourcePDO->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);

    // Si hay datos, insertarlos en la tabla destino
    if (!empty($data)) {
        foreach ($data as $row) {
            // Preparar los nombres de las columnas y los valores para la consulta
            $columns = implode(", ", array_keys($row));
            $values = implode(", ", array_map(function ($value) use ($targetPDO) {
                return $targetPDO->quote($value); // Escapar los valores
            }, array_values($row)));

            // Insertar los datos en la tabla destino
            $insertSQL = "INSERT INTO $table ($columns) VALUES ($values)";
            try {
                $targetPDO->exec($insertSQL);
            } catch (PDOException $e) {
                echo "Error al insertar datos en '$table': " . $e->getMessage() . "\n";
            }
        }
        echo "Datos copiados exitosamente para la tabla $table.\n";
    } else {
        echo "No hay datos para copiar en la tabla $table.\n";
    }
}

// Copiar los datos de las tablas
foreach ($tables as $table) {
    copiarDatos($sourcePDO, $targetPDO, $table);
}

echo "Estructura y datos replicados exitosamente en la base de datos destino.\n";
